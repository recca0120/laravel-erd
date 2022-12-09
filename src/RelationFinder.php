<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;
use Throwable;

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
                return [$method->getName() => $this->belongsToMany($return, $type, $related)];
            }

            if ($return instanceof BelongsTo) {
                return [$method->getName() => $this->belongsTo($return, $type, $related)];
            }

            if ($return instanceof HasOneOrMany) {
                return [$method->getName() => $this->hasOneOrMany($return, $type, $related)];
            }
        } catch (RuntimeException|ReflectionException $e) {
//            dump($method->getName());
//            dump($e->getMessage());
        } catch (Throwable $e) {

        }

        return null;
    }

    private function belongsToMany(BelongsToMany $return, string $type, string $related): Relation
    {
//        dump([
//            'getExistenceCompareKey' => $return->getExistenceCompareKey(),
//            'getForeignPivotKeyName' => $return->getForeignPivotKeyName(),
//            'getQualifiedForeignPivotKeyName' => $return->getQualifiedForeignPivotKeyName(),
//            'getRelatedPivotKeyName' => $return->getRelatedPivotKeyName(),
//            'getQualifiedRelatedPivotKeyName' => $return->getQualifiedRelatedPivotKeyName(),
//            'getParentKeyName' => $return->getParentKeyName(),
//            'getQualifiedParentKeyName' => $return->getQualifiedParentKeyName(),
//            'getRelatedKeyName' => $return->getRelatedKeyName(),
//            'getQualifiedRelatedKeyName' => $return->getQualifiedRelatedKeyName(),
//            'getRelationName' => $return->getRelationName(),
//            'getPivotAccessor' => $return->getPivotAccessor(),
//            'getPivotColumns' => $return->getPivotColumns(),
//        ]);

        $pivot = [
            'local_key' => $return->getQualifiedRelatedPivotKeyName(),
            'foreign_key' => $return->getQualifiedRelatedKeyName(),
        ];

        if ($return instanceof MorphToMany) {
//            dump([
//                'getMorphType' => $return->getMorphType(),
//                'getMorphClass' => $return->getMorphClass(),
//            ]);

            $pivot = array_merge([
                'morph_class' => $return->getMorphClass(),
                'morph_type' => $return->getMorphType(),
            ], $pivot);
        }

        return new Relation([
            'type' => $type,
            'related' => $related,
            'local_key' => $return->getQualifiedParentKeyName(),
            'foreign_key' => $return->getQualifiedForeignPivotKeyName(),
            'pivot' => new Pivot($pivot)
        ]);

    }

    private function belongsTo(BelongsTo $return, string $type, string $related): ?Relation
    {
//        dump([
//            'getForeignKeyName' => $return->getForeignKeyName(),
//            'getQualifiedForeignKeyName' => $return->getQualifiedForeignKeyName(),
//            'getParentKey' => $return->getParentKey(),
//            'getOwnerKeyName' => $return->getOwnerKeyName(),
//            'getQualifiedOwnerKeyName' => $return->getQualifiedOwnerKeyName(),
//            'getRelationName' => $return->getRelationName(),
//        ]);

        if ($return instanceof MorphTo) {
//            dump([
//                'getMorphType' => $return->getMorphType(),
//                'getDictionary' => $return->getDictionary(),
//            ]);
            return null;
        }

        return new Relation([
            'type' => $type,
            'related' => $related,
            'local_key' => $return->getQualifiedForeignKeyName(),
            'foreign_key' => $return->getQualifiedOwnerKeyName(),
        ]);
    }

    private function hasOneOrMany(HasOneOrMany $return, string $type, string $related): ?Relation
    {
        if ($return instanceof HasOne && $return->isOneOfMany()) {
            return null;
        }

//        dump([
//            'getQualifiedParentKeyName' => $return->getQualifiedParentKeyName(),
//            'getQualifiedForeignKeyName' => $return->getQualifiedForeignKeyName(),
//        ]);
        $attributes = [
            'type' => $type,
            'related' => $related,
            'local_key' => $return->getQualifiedParentKeyName(),
            'foreign_key' => $return->getQualifiedForeignKeyName(),
        ];

        if ($return instanceof MorphOneOrMany) {
//            dump([
//                'getQualifiedMorphType' => $return->getQualifiedMorphType(),
//                'getMorphClass' => $return->getMorphClass(),
//            ]);
            $attributes = array_merge($attributes, [
                'morph_type' => $return->getQualifiedMorphType(),
                'morph_class' => $return->getMorphClass(),
            ]);
        }

        return new Relation($attributes);
    }

    private function getTraitMethods(ReflectionClass $class): Collection
    {
        return collect($class->getTraits())->flatMap(
            static fn(ReflectionClass $trait) => $trait->getMethods(ReflectionMethod::IS_PUBLIC)
        );
    }
}