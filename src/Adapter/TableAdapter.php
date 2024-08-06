<?php

namespace Recca0120\LaravelErd\Adapter;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Illuminate\Support\Collection;

class TableAdapter
{

    private Table $table;

    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    public function getName(): string
    {
        return $this->table->getName();
    }

    /**
     * @return Collection<int, ColumnAdapter>
     */
    public function getColumns(): Collection
    {
        return collect($this->table->getColumns())->map(function (Column $column) {
            return new ColumnAdapter($column);
        });
    }

    public function getPrimaryKeys(): Collection
    {
        $primaryKey = $this->table->getPrimaryKey();

        return collect($primaryKey ? $primaryKey->getColumns() : []);
    }
}