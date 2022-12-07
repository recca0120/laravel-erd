<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;

class RelationFinder
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @throws ReflectionException
     */
    public function generate(string $model)
    {
        $class = new ReflectionClass($model);
        return collect($class->getMethods(ReflectionMethod::IS_PUBLIC))
            ->merge($this->getTraitMethods($class))
            ->reject(fn(ReflectionMethod $method) => $method->class !== $model)
            ->flatMap(fn(ReflectionMethod $method) => $this->makeModel($method, $model))
            ->filter();
    }

    private function makeModel(ReflectionMethod $method, string $model)
    {
        try {
            $return = $method->invoke($this->container->make($model));

            if (!$return instanceof Relation) {
                return;
            }

            $localKey = null;
            $foreignKey = null;

            if ($return instanceof HasOneOrMany) {
                $localKey = $this->getParentKey($return->getQualifiedParentKeyName());
                $foreignKey = $return->getForeignKeyName();

                return $this->getRelation($method, $return, $localKey, $foreignKey);
            }

            if ($return instanceof BelongsTo) {
                $foreignKey = $this->getParentKey($return->getQualifiedOwnerKeyName());
                $localKey = method_exists($return, 'getForeignKeyName')
                    ? $return->getForeignKeyName()
                    : $return->getForeignKey();

                return $this->getRelation($method, $return, $localKey, $foreignKey);
            }
        } catch (Throwable $e) {
            return null;
        }
    }

    private function getRelation(ReflectionMethod $method, Relation $return, string $localKey, string $foreignKey): array
    {
        return [
            $method->getName() => [
                $method->getShortName(),
                (new ReflectionClass($return))->getShortName(),
                (new ReflectionClass($return->getRelated()))->getName(),
                $localKey,
                $foreignKey
            ]
        ];
    }

    private function getParentKey(string $qualifiedKeyName): string
    {
        return collect(explode('.', $qualifiedKeyName))->last();
    }

    private function getTraitMethods(ReflectionClass $class): Collection
    {
        return collect($class->getTraits())->flatMap(
            static fn(ReflectionClass $trait) => $trait->getMethods(ReflectionMethod::IS_PUBLIC)
        );
    }


}