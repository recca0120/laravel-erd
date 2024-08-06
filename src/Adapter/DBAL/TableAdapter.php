<?php

namespace Recca0120\LaravelErd\Adapter\DBAL;

use Doctrine\DBAL\Schema\Column as DBALColumn;
use Doctrine\DBAL\Schema\Table as DBALTable;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Contracts\TableAdapterInterface;

class TableAdapter implements TableAdapterInterface
{
    private DBALTable $table;

    public function __construct(DBALTable $table)
    {
        $this->table = $table;
    }

    public function getName(): string
    {
        return $this->table->getName();
    }

    public function getColumns(): Collection
    {
        return collect($this->table->getColumns())->map(function (DBALColumn $column) {
            return new ColumnAdapter($column);
        });
    }

    public function getPrimaryKeys(): Collection
    {
        $primaryKey = $this->table->getPrimaryKey();

        return collect($primaryKey ? $primaryKey->getColumns() : []);
    }
}