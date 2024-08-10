<?php

namespace Recca0120\LaravelErd;

use Illuminate\Database\Eloquent\Model;
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
        $models = $this->mergeMissing($models);
        $relations = $models
            ->flatMap(fn (string $model) => RelationFinder::generate($model)->collapse())
            ->flatMap(fn (Relation $relation) => [$relation, $relation->relatedRelation()]);

        $relationGroupByConnection = $relations
            ->groupBy(fn (Relation $relation) => $relation->connection())
            ->map(function (Collection $relations) use ($excludes) {
                return $relations
                    ->reject(fn (Relation $relation) => $relation->excludes($excludes))
                    ->sortBy(fn (Relation $relation) => $relation->unique())
                    ->unique(fn (Relation $relation) => $relation->unique())
                    ->groupBy(fn (Relation $relation) => $relation->localTable());
            });

        return $models
            ->map(fn (string $model) => new $model())
            ->map(fn (Model $model) => [
                'connection' => $model->getConnectionName(),
                'table' => $model->getTable(),
                'related' => get_class($model),
            ])
            ->merge($relations->flatMap(function (Relation $relation) {
                $related = $relation->related();
                $connection = $relation->connection();

                return array_map(static fn (string $table) => [
                    'connection' => $connection,
                    'table' => $table,
                    'related' => $related,
                ], [$relation->foreignTable()]);
            }))
            ->unique(fn (array $data) => [$data['connection'], $data['table']])
            ->reject(fn (array $data) => in_array($data['table'], $excludes, true))
            ->sortBy(fn (array $data) => [$data['connection'], $data['table']])
            ->map(fn (array $data) => array_merge($data, [
                'relations' => $relationGroupByConnection
                    ->get($data['connection'], collect())
                    ->get($data['table'], collect()),
            ]))
            ->map(function (array $data) {
                return new Table($this->schemaBuilder->getTableSchema($data['table']), $data['relations']);
            });
    }

    private function mergeMissing(Collection $models): Collection
    {
        return $models->merge($models
            ->flatMap(fn (string $model) => RelationFinder::generate($model)->collapse())
            ->map(fn (Relation $relation) => $relation->related())
            ->filter()
            ->diff($models));
    }
}
