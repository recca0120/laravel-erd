<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

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

    public function related(): string
    {
        return $this->attributes['related'];
    }

    public function localKey(): string
    {
        return $this->attributes['local_key'];
    }

    public function foreignKey(): string
    {
        return $this->attributes['foreign_key'];
    }

    public function morphClass()
    {
        return $this->attributes['morph_class'] ?? '';
    }

    public function morphType()
    {
        return $this->attributes['morph_type'] ?? '';
    }

    public function pivot(): ?Pivot
    {
        return $this->attributes['pivot'] ?? null;
    }

    public function relationships(): Collection
    {
        $type = $this->type();

        if ($type === HasOne::class || $type === MorphOne::class) {
            return collect([
                new Relationship($type, $this->localKey(), $this->foreignKey())
            ]);
        }

        if ($type === HasMany::class || $type === MorphMany::class) {
            return collect([
                new Relationship($type, $this->localKey(), $this->foreignKey())
            ]);
        }

        if ($type === BelongsTo::class) {
            return collect([
                new Relationship($type, $this->localKey(), $this->foreignKey())
            ]);
        }

        if ($type === BelongsToMany::class || $type === MorphToMany::class) {
            /** @var Pivot $pivot */
            $pivot = $this->pivot();

            return collect([
                new Relationship($type, $this->localKey(), $this->foreignKey()),
                new Relationship($type, $pivot->localKey(), $pivot->foreignKey()),
            ]);
        }

        return collect();
    }
}