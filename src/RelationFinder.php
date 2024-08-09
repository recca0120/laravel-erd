<?php

namespace Recca0120\LaravelErd;

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
     * @param  class-string<Model>  $className
     * @return Collection<string, Collection<int, Relation>|null>
     *
     * @throws ReflectionException
     */
    public static function generate(string $className): Collection
    {
        $class = new ReflectionClass($className);
        $model = new $className;

        return collect($class->getMethods(ReflectionMethod::IS_PUBLIC))
            ->merge(self::getTraitMethods($class))
            ->reject(function (ReflectionMethod $method) use ($className) {
                return $method->class !== $className || $method->getNumberOfParameters() > 0;
            })
            ->mapWithKeys(fn (ReflectionMethod $method) => [
                $method->getName() => self::findRelations($model, $method),
            ])
            ->filter();
    }

    /**
     * @return ?Collection<int, Relation>
     */
    private static function findRelations(Model $model, ReflectionMethod $method): ?Collection
    {
        $attributes = self::getRelationAttributes($model, $method);

        return $attributes ? self::makeRelation($attributes) : null;
    }

    private static function getRelationAttributes(Model $model, ReflectionMethod $method): ?array
    {
        try {
            $return = $method->invoke($model);

            if (! $return instanceof EloquentRelation) {
                return null;
            }

            $type = (new ReflectionClass($return))->getName();
            $related = (new ReflectionClass($return->getRelated()))->getName();

            if ($return instanceof BelongsToMany) {
                return self::belongsToMany($return, $type, $related);
            }

            if ($return instanceof BelongsTo) {
                return self::belongsTo($return, $type, $related);
            }

            if ($return instanceof HasOneOrMany) {
                return self::hasOneOrMany($return, $type, $related);
            }
        } catch (RuntimeException|ReflectionException|Throwable $e) {
            // dump($method->getName());
            // dump($e->getMessage());
        }

        return null;
    }

    private static function belongsToMany(BelongsToMany $return, string $type, string $related): array
    {
        // dump([
        //     'getExistenceCompareKey' => $return->getExistenceCompareKey(),
        //     'getForeignPivotKeyName' => $return->getForeignPivotKeyName(),
        //     'getQualifiedForeignPivotKeyName' => $return->getQualifiedForeignPivotKeyName(),
        //     'getRelatedPivotKeyName' => $return->getRelatedPivotKeyName(),
        //     'getQualifiedRelatedPivotKeyName' => $return->getQualifiedRelatedPivotKeyName(),
        //     'getParentKeyName' => $return->getParentKeyName(),
        //     'getQualifiedParentKeyName' => $return->getQualifiedParentKeyName(),
        //     'getRelatedKeyName' => $return->getRelatedKeyName(),
        //     'getQualifiedRelatedKeyName' => $return->getQualifiedRelatedKeyName(),
        //     'getRelationName' => $return->getRelationName(),
        //     'getPivotAccessor' => $return->getPivotAccessor(),
        //     'getPivotColumns' => $return->getPivotColumns(),
        // ]);

        $parent = get_class($return->getParent());
        $pivot = [
            'type' => $type,
            'related' => $related,
            'parent' => $parent,
            'local_key' => $return->getQualifiedRelatedPivotKeyName(),
            'foreign_key' => $return->getQualifiedRelatedKeyName(),
        ];

        if ($return instanceof MorphToMany) {
            // dump([
            //     'getMorphType' => $return->getMorphType(),
            //     'getMorphClass' => $return->getMorphClass(),
            // ]);

            $pivot = array_merge([
                'morph_class' => $return->getMorphClass(),
                'morph_type' => $return->getMorphType(),
            ], $pivot);
        }

        return [
            'type' => $type,
            'related' => $related,
            'parent' => $parent,
            'local_key' => $return->getQualifiedParentKeyName(),
            'foreign_key' => $return->getQualifiedForeignPivotKeyName(),
            'pivot' => $pivot,
        ];
    }

    private static function belongsTo(BelongsTo $return, string $type, string $related): ?array
    {
        // dump([
        //     'getForeignKeyName' => $return->getForeignKeyName(),
        //     'getQualifiedForeignKeyName' => $return->getQualifiedForeignKeyName(),
        //     'getParentKey' => $return->getParentKey(),
        //     'getOwnerKeyName' => $return->getOwnerKeyName(),
        //     'getQualifiedOwnerKeyName' => $return->getQualifiedOwnerKeyName(),
        //     'getRelationName' => $return->getRelationName(),
        // ]);

        if ($return instanceof MorphTo) {
            // dump([
            //     'getMorphType' => $return->getMorphType(),
            //     'getDictionary' => $return->getDictionary(),
            // ]);
            return null;
        }

        return [
            'type' => $type,
            'related' => $related,
            'parent' => get_class($return->getParent()),
            'local_key' => $return->getQualifiedForeignKeyName(),
            'foreign_key' => $return->getQualifiedOwnerKeyName(),
        ];
    }

    private static function hasOneOrMany(HasOneOrMany $return, string $type, string $related): ?array
    {
        if ($return instanceof HasOne && $return->isOneOfMany()) {
            return null;
        }

        // dump([
        //     'getQualifiedParentKeyName' => $return->getQualifiedParentKeyName(),
        //     'getQualifiedForeignKeyName' => $return->getQualifiedForeignKeyName(),
        // ]);

        $attributes = [
            'type' => $type,
            'related' => $related,
            'parent' => get_class($return->getParent()),
            'local_key' => $return->getQualifiedParentKeyName(),
            'foreign_key' => $return->getQualifiedForeignKeyName(),
        ];

        if ($return instanceof MorphOneOrMany) {
            // dump([
            //     'getQualifiedMorphType' => $return->getQualifiedMorphType(),
            //     'getMorphClass' => $return->getMorphClass(),
            // ]);
            $attributes = array_merge($attributes, [
                'morph_type' => $return->getQualifiedMorphType(),
                'morph_class' => $return->getMorphClass(),
            ]);
        }

        return $attributes;
    }

    /**
     * @param  ReflectionClass<Model>  $class
     * @return Collection<int, ReflectionMethod>
     */
    private static function getTraitMethods(ReflectionClass $class): Collection
    {
        return collect($class->getTraits())->flatMap(
            static fn (ReflectionClass $trait) => $trait->getMethods(ReflectionMethod::IS_PUBLIC)
        );
    }

    /**
     * @param  string[]  $attributes
     * @return Collection<int, Relation>
     */
    private static function makeRelation(array $attributes): Collection
    {
        $relation = new Relation($attributes);
        $relations = collect([$relation]);

        $pivot = $relation->pivot();
        if ($pivot) {
            $relations->add(new Relation($pivot->toArray()));
        }

        return $relations;
    }
}
