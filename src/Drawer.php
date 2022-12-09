<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Drawer
{
    private Relation $relation;

    /** @var string[] */
    private array $relations = [
        BelongsTo::class => '*--1',
        HasOne::class => '1--1',
        MorphOne::class => '1--1',
        HasMany::class => '1--*',
        MorphMany::class => '1--*',
        BelongsToMany::class => '*--*',
    ];

    public function __construct(Relation $relation)
    {
        $this->relation = $relation;
    }

    public function draw(): array
    {
        $type = $this->relation->type();

        if ($type === HasOne::class || $type === MorphOne::class) {
            $localKey = $this->relation->localKey();
            $foreignKey = $this->relation->foreignKey();

            return [vsprintf(
                '%s %s %s',
                [
                    $this->getTableName($localKey),
                    $this->relations[$type],
                    $this->getTableName($foreignKey),
                ]
            )];
        }

        if ($type === HasMany::class || $type === MorphMany::class) {
            $localKey = $this->relation->localKey();
            $foreignKey = $this->relation->foreignKey();

            return [vsprintf(
                '%s %s %s',
                [
                    $this->getTableName($localKey),
                    $this->relations[$type],
                    $this->getTableName($foreignKey),
                ]
            )];
        }

        if ($type === BelongsTo::class) {
            $localKey = $this->relation->localKey();
            $foreignKey = $this->relation->foreignKey();

            return [vsprintf(
                '%s %s %s',
                [
                    $this->getTableName($localKey),
                    $this->relations[$type],
                    $this->getTableName($foreignKey),
                ]
            )];
        }

        if ($type === MorphToMany::class || $type === BelongsToMany::class) {
            $localKey = $this->relation->localKey();
            $foreignKey = $this->relation->foreignKey();

            return [
                vsprintf(
                    '%s %s %s',
                    [
                        $this->getTableName($localKey),
                        $this->relations[BelongsToMany::class],
                        $this->getTableName($foreignKey),
                    ]
                ),
                vsprintf(
                    '%s %s %s',
                    [
                        $this->getTableName($this->relation->pivot()->localKey()),
                        $this->relations[BelongsToMany::class],
                        $this->getTableName($this->relation->pivot()->foreignKey()),
                    ]
                )
            ];
        }

        return [];
    }

    private function getTableName(string $relation)
    {
        return substr($relation, 0, strpos($relation, '.'));
    }

}