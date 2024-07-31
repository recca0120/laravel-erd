<?php

namespace Recca0120\LaravelErd\Schema\Laravel;

use Recca0120\LaravelErd\Contracts\ColumnSchema as ColumnSchemaContract;

class ColumnSchema implements ColumnSchemaContract
{
    private array $column;

    public function __construct(array $column)
    {
        $this->column = $column;
    }

    public function getName(): string
    {
        return $this->column['name'];
    }

    public function isNullable(): bool
    {
        return $this->column['nullable'];
    }

    public function getPrecision(): int
    {
        $lookup = ['varchar' => 255, 'datetime' => 0, 'integer' => 11];

        return $lookup[$this->getType()] ?? 0;
    }

    public function getType(): string
    {
        return $this->column['type'];
    }

    public function getDefault()
    {
        $default = $this->column['default'];

        return $default ? trim($default, "'") : $default;
    }

    public function getComment(): ?string
    {
        return $this->column['comment'] ?? null;
    }

    public function isAutoIncrement(): bool
    {
        return $this->column['auto_increment'];
    }
}
