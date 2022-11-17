<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Schema\Column as DBALColumn;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\TypeRegistry;

class Column
{
    private DBALColumn $column;

    public function __construct(DBALColumn $column)
    {
        $this->column = $column;
    }

    public function name(): string
    {
        return $this->column->getName();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function type(): string
    {
        return $this->getTypeRegistry()->lookupName($this->column->getType());
    }


    private function getTypeRegistry(): TypeRegistry
    {
        return Type::getTypeRegistry();
    }

    public function nullable(): bool
    {
        return !$this->column->getNotnull();
    }

    public function default(): ?string
    {
        return $this->column->getDefault();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function render(): string
    {
        return sprintf(
            '%s {label: "%s, %s"}',
            $this->name(),
            $this->type(),
            $this->nullable() ? 'null' : 'not null'
        );
    }
}