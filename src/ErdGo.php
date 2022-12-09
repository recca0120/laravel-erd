<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ErdGo
{
    private ModelFinder $modelFinder;
    private RelationFinder $relationFinder;

    public function __construct(ModelFinder $modelFinder, RelationFinder $relationFinder)
    {
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

        $models
            ->map(fn(string $model) => new $model)
            ->map(fn(Model $model) => $model->getTable())
            ->each(function (string $table) {
                collect(Schema::getColumnListing($table))->each(function (string $column) use ($table) {
                    dump(Schema::getColumnType($table, $column));
                });
            })
            ->dd();

        $models
            ->flatMap(fn($model) => $this->relationFinder->generate($model)->values())
            ->flatMap(fn(Relation $relation) => $relation->all())
            ->unique(fn(Drawer $drawer) => $drawer->hash())
            ->map(fn(Drawer $drawer) => $drawer->draw())
            ->values()
            ->each(function ($draw) {
                echo $draw . "\n";
            });
    }
}