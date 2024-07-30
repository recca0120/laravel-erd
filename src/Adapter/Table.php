<?php

namespace Recca0120\LaravelErd\Adapter;

use Doctrine\DBAL\Schema\Column as DBALColumn;
use Doctrine\DBAL\Schema\Table as DBALTable;
use Illuminate\Support\Collection;

class Table
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

    /**
     * @return Collection<int, string>
     */
    public function getPrimaryKey(): Collection
    {
        $primaryKey = $this->table->getPrimaryKey();

        return collect($primaryKey ? $primaryKey->getColumns() : []);
    }
}
