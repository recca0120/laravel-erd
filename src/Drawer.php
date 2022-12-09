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
    /** @var string[] */
    private static array $relations = [
        BelongsTo::class => '1--1',
        HasOne::class => '1--1',
        MorphOne::class => '1--1',
        HasMany::class => '1--*',
        MorphMany::class => '1--*',
        BelongsToMany::class => '*--*',
        MorphToMany::class => '*--*'
    ];
    private string $type;
    private string $localKey;
    private string $foreignKey;

    public function __construct(string $type, string $localKey, string $foreignKey)
    {
        $this->type = $type;
        $this->localKey = $localKey;
        $this->foreignKey = $foreignKey;
    }

    public function draw(): string
    {
        return sprintf(
            '%s %s %s',
            $this->getTableName($this->localKey),
            self::$relations[$this->type],
            $this->getTableName($this->foreignKey)
        );
    }

    private function getTableName(string $qualifiedKeyName)
    {
        return substr($qualifiedKeyName, 0, strpos($qualifiedKeyName, '.'));
    }
}