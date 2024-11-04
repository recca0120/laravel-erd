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

    public function type(): string
    {
        return $this->attributes['type'];
    }

    public function related(): string
    {
        return $this->attributes['related'];
    }

    public function parent(): string
    {
        return $this->attributes['parent'];
    }

    public function localKey(): string
    {
        return $this->attributes['local_key'];
    }

    public function localTable(): string
    {
        return Helpers::getTableName($this->attributes['local_key']);
    }

    public function foreignKey(): string
    {
        return $this->attributes['foreign_key'];
    }

    public function foreignTable(): string
    {
        return Helpers::getTableName($this->attributes['foreign_key']);
    }

    public function morphClass(): string
    {
        return $this->attributes['morph_class'] ?? '';
    }

    public function morphType(): string
    {
        return $this->attributes['morph_type'] ?? '';
    }

    public function connection(): ?string
    {
        $model = $this->related();

        return (new $model)->getConnectionName();
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
