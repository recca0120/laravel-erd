<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Drawer
{
    private Relation $relation;

    private array $relations = [
        BelongsTo::class => '*--1',
        HasOne::class => '1--1',
        MorphOne::class => '1--1',
        HasMany::class => '1--*',
        MorphMany::class => '1--*',
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
            $relation = $this->relations[$type];

            return [
                vsprintf(
                    '%s %s %s',
                    [
                        $this->getTableName($localKey),
                        $relation,
                        $this->getTableName($foreignKey),
                    ]
                )
            ];
        }

        if ($type === HasMany::class || $type === MorphMany::class) {
            $localKey = $this->relation->localKey();
            $foreignKey = $this->relation->foreignKey();
            $relation = $this->relations[$type];

            return [
                vsprintf(
                    '%s %s %s',
                    [
                        $this->getTableName($localKey),
                        $relation,
                        $this->getTableName($foreignKey),
                    ]
                )
            ];
        }

        if ($type === BelongsTo::class) {
            $localKey = $this->relation->localKey();
            $foreignKey = $this->relation->foreignKey();
            $relation = $this->relations[$type];

            return [
                vsprintf(
                    '%s %s %s',
                    [
                        $this->getTableName($localKey),
                        $relation,
                        $this->getTableName($foreignKey),
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

    private function getKeyName(string $relation)
    {
        return substr($relation, strpos($relation, '.') + 1);
    }
}