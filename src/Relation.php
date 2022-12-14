<?php

namespace Recca0120\LaravelErd;

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

    public function relatedRelation(): Relation
    {
        return new Relation([
            'type' => $this->type(),
            'local_key' => $this->foreignKey(),
            'foreign_key' => $this->localKey(),
        ]);
    }
}