<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

class RelationFinder
{
    /**
     * @param string $className
     * @return Collection<string, Relation>
     * @throws ReflectionException
     */
    public function generate(string $className): Collection
    {
        $class = new ReflectionClass($className);
        $model = new $className;

        return collect($class->getMethods(ReflectionMethod::IS_PUBLIC))
            ->merge($this->getTraitMethods($class))
            ->reject(fn(ReflectionMethod $method) => $method->class !== $className || $method->getNumberOfParameters() > 0)
            ->flatMap(fn(ReflectionMethod $method) => $this->findRelations($method, $model))
            ->filter();
    }

    private function findRelations(ReflectionMethod $method, Model $model): ?array
    {
        try {
            $return = $method->invoke($model);

            if (!$return instanceof EloquentRelation) {
                return null;
            }

            $type = (new ReflectionClass($return))->getName();
            $related = (new ReflectionClass($return->getRelated()))->getName();


            if ($return instanceof BelongsToMany) {
//                dump([
//                    'getExistenceCompareKey' => $return->getExistenceCompareKey(),
//                    'getForeignPivotKeyName' => $return->getForeignPivotKeyName(),
//                    'getQualifiedForeignPivotKeyName' => $return->getQualifiedForeignPivotKeyName(),
//                    'getRelatedPivotKeyName' => $return->getRelatedPivotKeyName(),
//                    'getQualifiedRelatedPivotKeyName' => $return->getQualifiedRelatedPivotKeyName(),
//                    'getParentKeyName' => $return->getParentKeyName(),
//                    'getQualifiedParentKeyName' => $return->getQualifiedParentKeyName(),
//                    'getRelatedKeyName' => $return->getRelatedKeyName(),
//                    'getQualifiedRelatedKeyName' => $return->getQualifiedRelatedKeyName(),
//                    'getRelationName' => $return->getRelationName(),
//                    'getPivotAccessor' => $return->getPivotAccessor(),
//                    'getPivotColumns' => $return->getPivotColumns(),
//                ]);

                $pivot = [
                    'local_key' => $return->getQualifiedRelatedPivotKeyName(),
                    'foreign_key' => $return->getQualifiedRelatedKeyName(),
                ];

                if ($return instanceof MorphToMany) {
//                    dump([
//                        'getMorphType' => $return->getMorphType(),
//                        'getMorphClass' => $return->getMorphClass(),
//                    ]);

                    $pivot = array_merge([
                        'morph_class' => $return->getMorphClass(),
                        'morph_type' => $return->getMorphType(),
                    ], $pivot);
                }

                return [
                    $method->getName() => new Relation([
                        'type' => $type,
                        'related' => $related,
                        'local_key' => $return->getQualifiedParentKeyName(),
                        'foreign_key' => $return->getQualifiedForeignPivotKeyName(),
                        'pivot' => new Pivot($pivot)
                    ])
                ];
            }

            if ($return instanceof BelongsTo) {
//                dump([
//                    'getForeignKeyName' => $return->getForeignKeyName(),
//                    'getQualifiedForeignKeyName' => $return->getQualifiedForeignKeyName(),
//                    'getParentKey' => $return->getParentKey(),
//                    'getOwnerKeyName' => $return->getOwnerKeyName(),
//                    'getQualifiedOwnerKeyName' => $return->getQualifiedOwnerKeyName(),
//                    'getRelationName' => $return->getRelationName(),
//                ]);

                return [
                    $method->getName() => new Relation([
                        'type' => $type,
                        'related' => $related,
                        'local_key' => $return->getQualifiedForeignKeyName(),
                        'foreign_key' => $return->getQualifiedOwnerKeyName(),
                    ])
                ];
            }

            if ($return instanceof HasOneOrMany) {
//                dump([
//                    'getQualifiedParentKeyName' => $return->getQualifiedParentKeyName(),
//                    'getQualifiedForeignKeyName' => $return->getQualifiedForeignKeyName(),
//                ]);

                return [
                    $method->getName() => new Relation([
                        'type' => $type,
                        'related' => $related,
                        'local_key' => $return->getQualifiedParentKeyName(),
                        'foreign_key' => $return->getQualifiedForeignKeyName(),
                    ])
                ];
            }
        } catch (RuntimeException|ReflectionException $e) {
            dump($method->getName());
            dump($e->getMessage());
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