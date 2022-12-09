<?php

namespace Recca0120\LaravelErdGo;

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
            ->diff($models);

        $models
            ->merge($missing)
            ->flatMap(fn($model) => $this->relationFinder->generate($model)->values())
            ->flatMap(fn(Relation $relation) => $relation->all()->map->draw())
            ->unique()
            ->sort()
            ->values()
            ->each(function ($draw) {
                echo $draw . "\n";
            });
    }
}