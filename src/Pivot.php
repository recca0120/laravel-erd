<?php

namespace Recca0120\LaravelErd;

class Pivot
{
    /**
     * @var array<string, string>
     */
    private array $attributes;

    /**
     * @param  array<string, string>  $pivot
     */
    public function __construct(array $pivot)
    {
        $this->attributes = $pivot;
    }

    public function model(): string
    {
        return $this->attributes['model'];
    }

    public function connection(): ?string
    {
        return $this->attributes['connection'];
    }

    public function table(): string
    {
        return Helpers::getTableName($this->attributes['local_key']);
    }

    public function type(): string
    {
        return $this->attributes['type'];
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

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
