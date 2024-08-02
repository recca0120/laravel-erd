<?php

namespace Recca0120\LaravelErd;

use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Adapter\SchemaManagerAdapter;

class ErdFinder
{
    private SchemaManagerAdapter $schemaManager;

    private ModelFinder $modelFinder;

    private RelationFinder $relationFinder;

    private string $directory;

    public function __construct(
        SchemaManagerAdapter $schemaManager,
        ModelFinder $modelFinder,
        RelationFinder $relationFinder
    ) {
        $this->schemaManager = $schemaManager;
        $this->modelFinder = $modelFinder;
        $this->relationFinder = $relationFinder;
    }

    public function in(string $directory): ErdFinder
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * @param  string|string[]  $patterns
     * @param  string[]  $excludes
     * @return Collection<int|string, Table>
     */
    public function find($patterns = '*.php', array $excludes = []): Collection
    {
        $models = $this->modelFinder->find($this->directory ?? __DIR__, $patterns);

        return $this->findByModels($models, $excludes);
    }

    /**
     * @param  string|string[]  $file
     * @param  string[]  $excludes
     * @return Collection<int|string, Table>
     */
    public function findByFile($file, array $excludes = []): Collection
    {
        $models = $this->modelFinder->find($this->directory ?? __DIR__, $file);

        return $this->findByModels($models, $excludes);
    }

    /**
     * @param  string  $className
     * @param  string[]  $excludes
     * @return Collection<int|string, Table>
     */
    public function findByModel(string $className, array $excludes = []): Collection
    {
        return $this->findByModels(collect($className), $excludes);
    }

    /**
     * @param  Collection  $models
     * @param  string[]  $excludes
     * @return Collection<int|string, Table>
     */
    private function findByModels(Collection $models, array $excludes = []): Collection
    {
        $missing = $models
            ->flatMap(fn (string $model) => $this->relationFinder->generate($model)->collapse())
            ->map(fn (Relation $relation) => $relation->related())
            ->filter()
            ->diff($models);

        return $models
            ->merge($missing)
            ->flatMap(fn (string $model) => $this->relationFinder->generate($model)->collapse())
            ->flatMap(fn (Relation $relation) => [$relation, $relation->relatedRelation()])
            ->reject(fn (Relation $relation) => $relation->includes($excludes))
            ->sortBy(fn (Relation $relation) => $this->uniqueRelation($relation))
            ->unique(fn (Relation $relation) => $this->uniqueRelation($relation))
            ->groupBy(fn (Relation $relation) => $relation->table())
            ->sortBy(fn (Collection $relations, string $table) => $table)
            ->map(function (Collection $relations, string $table) {
                return new Table($this->schemaManager->introspectTable($table), $relations);
            });
    }

    /**
     * @param  Relation  $relation
     * @return string[]
     */
    private function uniqueRelation(Relation $relation): array
    {
        return [$relation->type(), $relation->localKey(), $relation->foreignKey()];
    }
}
