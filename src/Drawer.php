<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Database\Eloquent\Relations\HasOne;

class Drawer
{
    private Relation $relation;

    private array $relations = [
        HasOne::class => '1--1'
    ];

    public function __construct(Relation $relation)
    {
        $this->relation = $relation;
    }

    public function draw()
    {
        if ($this->relation->type() === HasOne::class) {
            $localKey = $this->relation->localKey();
            $foreignKey = $this->relation->foreignKey();
            $relation = $this->relations[$this->relation->type()];

            return vsprintf(
                '%s %s %s',
                [
                    $this->getTableName($localKey),
                    $relation,
                    $this->getTableName($foreignKey)
                ]
            );
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