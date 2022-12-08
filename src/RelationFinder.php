<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

//            if ($return instanceof MorphToMany) {
//                dump($return->getMorphType());
//                dump($return->getMorphClass());
//            }
//            if ($return instanceof BelongsToMany) {
//                dump($return->getExistenceCompareKey());
//                dump($return->getForeignPivotKeyName());
//                dump((new ReflectionClass($return->getRelated()))->getName());
//                dump($return->getQualifiedForeignPivotKeyName());
//                dump($return->getRelatedPivotKeyName());
//                dump($return->getQualifiedRelatedPivotKeyName());
//                dump($return->getParentKeyName());
//                dump($return->getQualifiedParentKeyName());
//                dump($return->getRelatedKeyName());
//                dump($return->getQualifiedRelatedKeyName());
//                dump($return->getRelationName());
//                dump($return->getPivotAccessor());
//                dump($return->getPivotColumns());
//                return;
//            }

//            $relationType = (new ReflectionClass($return))->getShortName();
//            $modelName = (new ReflectionClass($return->getRelated()))->getName();
//
//            $foreignKey = $return->getQualifiedForeignKeyName();
//            $parentKey = $return->getQualifiedParentKeyName();
//
//            return [
//                $method->getName() => [
//                    'type' => $relationType,
//                    'model' => $modelName,
//                    'foreign_key' => $foreignKey,
//                    'parent_key' => $parentKey,
//                ]
//            ];

            $type = (new ReflectionClass($return))->getShortName();
            $related = (new ReflectionClass($return->getRelated()))->getName();

            if ($return instanceof BelongsTo) {
//                dump($return->getForeignKeyName());
//                dump($return->getQualifiedForeignKeyName());
//                dump($return->getParentKey());
//                dump($return->getOwnerKeyName());
//                dump($return->getQualifiedOwnerKeyName());
//                dump($return->getRelationName());

                return [
                    $method->getName() => new Relation([
                        'type' => $type,
                        'related' => $related,
                        'local_key' => $return->getQualifiedForeignKeyName(),
                        'foreign_key' => $return->getQualifiedOwnerKeyName(),
                    ])
                ];

            }


            if ($return instanceof HasOne) {
//                dump($return->getQualifiedParentKeyName());
//                dump($return->getQualifiedForeignKeyName());
//                dump((new ReflectionClass($return->getRelated()))->getName());
//                dump((new ReflectionClass($return))->getShortName());
//                dump($method->name);

                return [
                    $method->getName() => new Relation([
                        'type' => $type,
                        'related' => $related,
                        'local_key' => $return->getQualifiedParentKeyName(),
                        'foreign_key' => $return->getQualifiedForeignKeyName(),
                    ])
                ];
            }
        } catch (RuntimeException $e) {
//            dump($method->getName());
//            dump($e->getMessage());
        } catch (ReflectionException $e) {
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