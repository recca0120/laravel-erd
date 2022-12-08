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
        return $this->getKeyName($this->attributes['local_key']);
    }

    public function foreignKey(): string
    {
        return $this->getKeyName($this->attributes['foreign_key']);
    }

    private function getKeyName(string $qualifiedKeyName)
    {
        $segments = explode('.', $qualifiedKeyName);

        return end($segments);
    }
}