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

        $relations = $models->flatMap(function (string $model) {
            return $this->relationFinder->generate($model)->flatMap(function (Relation $relation) {
                return $relation->draw();
            });
        });
    }
}