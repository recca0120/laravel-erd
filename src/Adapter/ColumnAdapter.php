<?php

namespace Recca0120\LaravelErd\Adapter;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;

class ColumnAdapter
{
    private Column $column;

    public function __construct(Column $column)
    {
        $this->column = $column;
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

    public function getName(): string
    {
        return $this->column->getName();
    }

    public function getNotnull(): bool
    {
        return $this->column->getNotnull();
    }

    public function getAutoincrement(): bool
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