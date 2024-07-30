<?php

namespace Recca0120\LaravelErd\Schema\DBAL;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Column as DBALColumn;
use Doctrine\DBAL\Types\Type;
use Recca0120\LaravelErd\Contracts\ColumnSchema as ColumnContract;

class ColumnSchema implements ColumnContract
{
    private DBALColumn $column;

    public function __construct(DBALColumn $column)
    {
        $this->column = $column;
    }

    public function getName(): string
    {
        return $this->column->getName();
    }

    public function isNullable(): bool
    {
        return ! $this->column->getNotnull();
    }

    public function getPrecision(): int
    {
        return $this->column->getPrecision();
    }

    public function getDefault()
    {
        return $this->column->getDefault();
    }

    public function getComment(): ?string
    {
        return $this->column->getComment();
    }

    public function isAutoIncrement(): bool
    {
        return $this->column->getAutoincrement();
    }

    public function getType(): string
    {
        try {
            return Type::getTypeRegistry()->lookupName($this->column->getType());
        } catch (Exception $e) {
            return 'unknown';
        }
    }
}
