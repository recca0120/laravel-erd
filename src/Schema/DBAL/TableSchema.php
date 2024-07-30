<?php

namespace Recca0120\LaravelErd\Schema\DBAL;

use Doctrine\DBAL\Schema\Column as DBALColumn;
use Doctrine\DBAL\Schema\Table as DBALTable;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Contracts\TableSchema as TableSchemaContract;

class TableSchema implements TableSchemaContract
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

    /**
     * @return Collection<int, ColumnSchema>
     */
    public function getColumns(): Collection
    {
        return collect($this->table->getColumns())->map(function (DBALColumn $column) {
            return new ColumnSchema($column);
        });
    }

    public function getPrimaryKey(): Collection
    {
        $primaryKey = $this->table->getPrimaryKey();

        return collect($primaryKey ? $primaryKey->getColumns() : []);
    }
}
