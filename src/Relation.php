<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Relation
{
    private array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }


    public function type(): string
    {
        return $this->attributes['type'];
    }

    public function related(): ?string
    {
        return $this->attributes['related'] ?? null;
    }

    public function table(): string
    {
        return Helpers::getTableName($this->localKey());
    }

    public function localKey(): string
    {
        return $this->attributes['local_key'];
    }

    public function foreignKey(): string
    {
        return $this->attributes['foreign_key'];
    }

    public function morphClass(): ?string
    {
        return $this->attributes['morph_class'] ?? null;
    }

    public function morphType(): ?string
    {
        return $this->attributes['morph_type'] ?? null;
    }

    public function pivot(): ?Pivot
    {
        return $this->attributes['pivot'] ?? null;
    }

    public function uniqueId(): string
    {
        $localKey = Helpers::getTableName($this->localKey());
        $foreignKey = Helpers::getTableName($this->foreignKey());

        $sortBy = [$localKey, $foreignKey];
        sort($sortBy);

        return implode('::', $sortBy);
    }

    public function sortBy(): int
    {
        if (in_array($this->type(), [BelongsTo::class, HasOne::class, MorphOne::class])) {
            return 3;
        }

        if (in_array($this->type(), [HasMany::class, MorphMany::class])) {
            return 2;
        }

        return 1;
    }

    /**
     * @param  string|string[]  $tables
     * @return bool
     */
    public function includes($tables): bool
    {
        $localTable = Helpers::getTableName($this->localKey());
        $foreignTable = Helpers::getTableName($this->foreignKey());

        return in_array($localTable, $tables, true) || in_array($foreignTable, $tables, true);
    }
}