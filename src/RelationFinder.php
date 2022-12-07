<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;

class RelationFinder
{
    /**
     * @throws ReflectionException
     */
    public function generate(string $className)
    {
        $class = new ReflectionClass($className);
        $model = new $className;

        return collect($class->getMethods(ReflectionMethod::IS_PUBLIC))
            ->merge($this->getTraitMethods($class))
            ->reject(fn(ReflectionMethod $method) => $method->class !== $className || $method->getNumberOfParameters() > 0)
            ->flatMap(fn(ReflectionMethod $method) => $this->findRelations($method, $model))
            ->filter();
    }

    private function findRelations(ReflectionMethod $method, Model $model)
    {
        try {
            $return = $method->invoke($model);

            if (!$return instanceof Relation) {
                return null;
            }

            $relationType = (new ReflectionClass($return))->getShortName();
            $modelName = (new ReflectionClass($return->getRelated()))->getName();

            $foreignKey = $return->getQualifiedForeignKeyName();
            $parentKey = $return->getQualifiedParentKeyName();

            return [
                $method->getName() => [
                    'type' => $relationType,
                    'model' => $modelName,
                    'foreign_key' => $foreignKey,
                    'parent_key' => $parentKey,
                ]
            ];
        } catch (Throwable $e) {
        }

        return null;
    }

    private function getTraitMethods(ReflectionClass $class): Collection
    {
        return collect($class->getTraits())->flatMap(
            static fn(ReflectionClass $trait) => $trait->getMethods(ReflectionMethod::IS_PUBLIC)
        );
    }


}