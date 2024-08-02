<?php

namespace Recca0120\LaravelErd\Adapter;

use Doctrine\DBAL\Schema\Table;
use Illuminate\Support\Collection;

class TableAdapter
{

    private Table $table;

    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    public function getName()
    {
        return $this->table->getName();
    }

    public function getColumns(): Collection
    {
        return collect($this->table->getColumns());
    }

    public function getPrimaryKeys()
    {
        $primaryKey = $this->table->getPrimaryKey();

        return $primaryKey ? $primaryKey->getColumns() : [];
    }
}