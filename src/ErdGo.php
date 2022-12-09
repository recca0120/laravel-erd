<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\Collection;

class ErdGo
{
    private AbstractSchemaManager $schemaManager;
    private ModelFinder $modelFinder;
    private RelationFinder $relationFinder;

    public function __construct(AbstractSchemaManager $schemaManager, ModelFinder $modelFinder, RelationFinder $relationFinder)
    {
        $this->schemaManager = $schemaManager;
        $this->modelFinder = $modelFinder;
        $this->relationFinder = $relationFinder;
    }

    public function generate(string $directory): Collection
    {
        $models = $this->modelFinder->find($directory);

        $missing = $models
            ->flatMap(fn(string $model) => $this->relationFinder->generate($model))
            ->map(fn(Relation $relation) => $relation->related())
            ->diff($models);

        $relations = $models
            ->merge($missing)
            ->flatMap(fn($model) => $this->relationFinder->generate($model)->values())
            ->flatMap(fn(Relation $relation) => $relation->relationships());

        $tables = $relations
            ->flatMap(fn(Relationship $drawer) => [$drawer->localKey(), $drawer->foreignKey()])
            ->map(fn(string $key) => $this->getTableName($key))
            ->sort()
            ->unique()
            ->map(fn(string $table) => new Table($table, $this->schemaManager->listTableColumns($table)))
            ->map(fn(Table $table): string => $table->render());

        $relationships = $relations
            ->unique(fn(Relationship $relationship) => $relationship->hash())
            ->map(fn(Relationship $relationship) => $relationship->render());

        return $tables->merge($relationships);
    }

    private function getTableName(string $qualifiedKeyName)
    {
        return substr($qualifiedKeyName, 0, strpos($qualifiedKeyName, '.'));
    }
}