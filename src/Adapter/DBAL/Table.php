<?php

namespace Recca0120\LaravelErd\Adapter\DBAL;

use Doctrine\DBAL\Schema\Column as DBALColumn;
use Doctrine\DBAL\Schema\Table as DBALTable;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Adapter\Contracts\Table as TableContract;

class Table implements TableContract
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
     * @return Collection<int, Column>
     */
    public function getColumns(): Collection
    {
        return collect($this->table->getColumns())->map(function (DBALColumn $column) {
            return new Column($column);
        });
    }

    public function getPrimaryKey(): Collection
    {
        $primaryKey = $this->table->getPrimaryKey();

        return collect($primaryKey ? $primaryKey->getColumns() : []);
    }
}
