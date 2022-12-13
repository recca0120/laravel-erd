<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Collection;

class ErdFinder
{
    private AbstractSchemaManager $schemaManager;
    private ModelFinder $modelFinder;
    private RelationFinder $relationFinder;
    private string $directory;

    public function __construct(
        AbstractSchemaManager $schemaManager,
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
     * @param  string|string[]  $excludes
     * @return Collection
     * @throws DBALException
     */
    public function find($patterns = '*.php', array $excludes = []): Collection
    {
        $models = $this->modelFinder->find($this->directory ?? __DIR__, $patterns);

        return $this->findByModels($models, $excludes);
    }

    /**
     * @throws DBALException
     */
    public function findByFile($file, array $excludes = []): Collection
    {
        $models = $this->modelFinder->find($this->directory ?? __DIR__, $file);

        return $this->findByModels($models, $excludes);
    }

    /**
     * @throws DBALException
     */
    public function findByModel($className, array $excludes = []): Collection
    {
        return $this->findByModels(collect($className), $excludes);
    }

    /**
     * @param  Collection  $models
     * @param  string|string[]  $excludes
     * @return Collection
     * @throws DBALException
     */
    private function findByModels(Collection $models, $excludes = []): Collection
    {
        /** @var Collection $missing */
        $missing = $models
            ->flatMap(fn(string $model) => $this->relationFinder->generate($model)->collapse())
            ->map(fn(Relation $relation) => $relation->related())
            ->filter()
            ->diff($models);


        return $models
            ->merge($missing)
            ->flatMap(fn(string $model) => $this->relationFinder->generate($model)->collapse())
            ->flatMap(fn(Relation $relation) => [$relation, $relation->relatedRelation()])
            ->sortBy(fn(Relation $relation) => $this->uniqueRelation($relation))
            ->unique(fn(Relation $relation) => $this->uniqueRelation($relation))
            ->groupBy(fn(Relation $relation) => $relation->table())
            ->sortBy(fn(Collection $relations, $table) => $table)
            ->map(function (Collection $relations, $table) {
                return new Table($this->schemaManager->introspectTable($table), $relations);
            });
    }

    private function uniqueRelation(Relation $relation): array
    {
        return [$relation->type(), $relation->localKey(), $relation->foreignKey()];
    }
}