<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Drawer
{
    private Relation $relation;

    private array $relations = [
        HasOne::class => '1--1',
        BelongsTo::class => '1--1'
    ];

    public function __construct(Relation $relation)
    {
        $this->relation = $relation;
    }

    public function draw()
    {
        $type = $this->relation->type();

        if ($type === HasOne::class) {
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