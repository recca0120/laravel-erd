<?php

namespace Recca0120\LaravelErdGo;


class Pivot
{
    private array $attributes;

    public function __construct(array $pivot)
    {
        $this->attributes = $pivot;
    }

    public function table(): string
    {
        return Helpers::getTableName($this->attributes['local_key']);
    }

    public function localKey(): string
    {
        return $this->attributes['local_key'];
    }

    public function foreignKey(): string
    {
        return $this->attributes['foreign_key'];
    }

    public function morphClass(): string
    {
        return $this->attributes['morph_class'] ?? '';
    }

    public function morphType(): string
    {
        return $this->attributes['morph_type'] ?? '';
    }
}