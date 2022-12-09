<?php

namespace Recca0120\LaravelErdGo;

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

    public function draw(): array
    {
        return (new Drawer($this))->draw();
    }

}