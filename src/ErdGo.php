<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Schema\AbstractSchemaManager;

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

    public function generate(string $directory): void
    {
        $models = $this->modelFinder->find($directory);

        $missing = $models
            ->flatMap(fn(string $model) => $this->relationFinder->generate($model))
            ->map(fn(Relation $relation) => $relation->related())
            ->diff($models)
            ->values();

        $models = $models->merge($missing);

//        $models
//            ->map(fn(string $model) => new $model)
//            ->map(fn(Model $model) => $model->getTable())
//            ->map(fn(string $table) => new Table($table, $this->schemaManager->listTableColumns($table)))
//            ->each(function (Table $table) {
//                $table->render();
//            });

        $relations = $models
            ->merge($missing)
            ->flatMap(fn($model) => $this->relationFinder->generate($model)->values())
            ->flatMap(fn(Relation $relation) => $relation->all());

        $relations
            ->flatMap(fn(Relationship $drawer) => [$drawer->localKey(), $drawer->foreignKey()])
            ->map(fn(string $key) => $this->getTableName($key))
            ->sort()
            ->unique()
            ->values()
            ->map(fn(string $table) => new Table($table, $this->schemaManager->listTableColumns($table)))
            ->each(function (Table $table) {
                echo $table->render() . "\n";
            });

        $relations
            ->unique(fn(Relationship $relationship) => $relationship->hash())
            ->map(fn(Relationship $relationship) => $relationship->render())
            ->values()
            ->each(function (string $relationship) {
                echo $relationship . "\n";
            });
    }

    private function getTableName(string $qualifiedKeyName)
    {
        return substr($qualifiedKeyName, 0, strpos($qualifiedKeyName, '.'));
    }
}