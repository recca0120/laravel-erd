<?php

namespace Recca0120\LaravelErd\Adapter\Laravel;

use Recca0120\LaravelErd\Adapter\Contracts\Column as ColumnContract;

class Column implements ColumnContract
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

    public function getNotnull(): bool
    {
        return ! $this->column['nullable'];
    }

    public function getPrecision(): int
    {
        return $this->column['precision'] ?? 10;
    }

    public function getColumnType(): string
    {
        $type = $this->column['type'];

        return $type === 'varchar' ? 'string' : $type;
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

    public function getAutoincrement(): bool
    {
        return $this->column['auto_increment'];
    }
}
