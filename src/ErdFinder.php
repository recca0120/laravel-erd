<?php

namespace Recca0120\LaravelErd;

use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Contracts\SchemaBuilder;
use ReflectionException;

class ErdFinder
{
    private SchemaBuilder $schemaBuilder;

    private ModelFinder $modelFinder;

    private string $directory;

    public function __construct(SchemaBuilder $schemaBuilder, ModelFinder $modelFinder)
    {
        $this->schemaBuilder = $schemaBuilder;
        $this->modelFinder = $modelFinder;
    }

    public function in(string $directory): ErdFinder
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * @param  string|string[]  $regex
     * @param  string[]  $excludes
     * @return Collection<int|string, Table>
     *
     * @throws ReflectionException
     */
    public function find($regex = '*.php', array $excludes = []): Collection
    {
        $models = $this->modelFinder->find($this->directory ?? __DIR__, $regex);

        return $this->findByModels($models, $excludes);
    }

    /**
     * @param  string|string[]  $file
     * @param  string[]  $excludes
     * @return Collection<int|string, Table>
     *
     * @throws ReflectionException
     */
    public function findByFile($file, array $excludes = []): Collection
    {
        $models = $this->modelFinder->find($this->directory ?? __DIR__, $file);

        return $this->findByModels($models, $excludes);
    }

    /**
     * @param  string[]  $excludes
     * @return Collection<int|string, Table>
     *
     * @throws ReflectionException
     */
    public function findByModel(string $className, array $excludes = []): Collection
    {
        return $this->findByModels(collect($className), $excludes);
    }

    /**
     * @param  string[]  $excludes
     * @return Collection<int|string, Table>
     *
     * @throws ReflectionException
     */
    private function findByModels(Collection $models, array $excludes = []): Collection
    {
        $missing = $models
            ->flatMap(fn (string $model) => RelationFinder::generate($model)->collapse())
            ->map(fn (Relation $relation) => $relation->related())
            ->filter()
            ->diff($models);

        $models = $models->merge($missing);

        $relations = $models
            ->flatMap(fn (string $model) => RelationFinder::generate($model)->collapse())
            ->flatMap(fn (Relation $relation) => [$relation, $relation->relatedRelation()])
            ->reject(fn (Relation $relation) => $relation->excludes($excludes))
            ->sortBy(fn (Relation $relation) => $relation->unique())
            ->unique(fn (Relation $relation) => $relation->unique())
            ->groupBy(fn (Relation $relation) => $relation->table())
            ->sortBy(fn (Collection $relations, string $table) => $table);

        $tables = $models
            ->map(fn (string $model) => (new $model())->getTable())
            ->merge($relations->collapse()->flatMap(fn (Relation $relation) => [
                $relation->table(), $relation->localTable(), $relation->foreignTable(),
            ]))
            ->unique()
            ->reject(fn (string $table) => in_array($table, $excludes))
            ->sort();

        return $tables
            ->mapWithKeys(fn (string $table) => [$table => $relations->get($table, collect())])
            ->map(function (Collection $relations, string $table) {
                return new Table($this->schemaBuilder->getTableSchema($table), $relations);
            });
    }
}
