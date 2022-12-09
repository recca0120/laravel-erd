<?php

namespace Recca0120\LaravelErdGo;

class Pivot
{
    private array $attributes;

    public function __construct(array $pivot)
    {
        $this->attributes = $pivot;
    }

    public function table()
    {
        return $this->getTableName($this->attributes['local_key']);
    }

    public function localKey()
    {
        return $this->attributes['local_key'];
    }

    public function foreignKey()
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

    private function getKeyName(string $qualifiedKeyName)
    {
        $segments = explode('.', $qualifiedKeyName);

        return end($segments);
    }

    private function getTableName(string $qualifiedKeyName)
    {
        $segments = explode('.', $qualifiedKeyName);

        return head($segments);
    }
}