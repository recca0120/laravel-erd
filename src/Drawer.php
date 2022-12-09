<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Recca0120\LaravelErdGo\Contracts\Drawable;

class Drawer
{
    private Relation $relation;

    /** @var string[] */
    private array $relations = [
        BelongsTo::class => '1--1',
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
            return [$this->doDraw($type, $this->relation)];
        }

        if ($type === HasMany::class || $type === MorphMany::class) {
            return [$this->doDraw($type, $this->relation)];
        }

        if ($type === BelongsTo::class) {
            return [$this->doDraw($type, $this->relation)];
        }

        if ($type === BelongsToMany::class || $type === MorphToMany::class) {
            return [
                $this->doDraw(BelongsToMany::class, $this->relation),
                $this->doDraw(BelongsToMany::class, $this->relation->pivot())
            ];
        }

        return [];
    }

    private function doDraw(string $type, Drawable $drawable): string
    {
        return vsprintf(
            '%s %s %s',
            [
                $this->getTableName($drawable->localKey()),
                $this->relations[$type],
                $this->getTableName($drawable->foreignKey()),
            ]
        );
    }

    private function getTableName(string $qualifiedKeyName)
    {
        return substr($qualifiedKeyName, 0, strpos($qualifiedKeyName, '.'));
    }
}