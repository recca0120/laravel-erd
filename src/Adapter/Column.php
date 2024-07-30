<?php

namespace Recca0120\LaravelErd\Adapter;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Column as DBALColumn;
use Doctrine\DBAL\Types\Type;

class Column
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

    public function getNotnull(): bool
    {
        return $this->column->getNotnull();
    }

    public function getPrecision(): int
    {
        return $this->column->getPrecision();
    }

    public function getColumnType(): string
    {
        try {
            return Type::getTypeRegistry()->lookupName($this->column->getType());
        } catch (Exception $e) {
            return 'unknown';
        }
    }

    public function getDefault()
    {
        return $this->column->getDefault();
    }

    public function getComment(): ?string
    {
        return $this->column->getComment();
    }

    public function getAutoincrement(): bool
    {
        return $this->column->getAutoincrement();
    }
}
