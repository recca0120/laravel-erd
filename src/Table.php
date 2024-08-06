<?php

namespace Recca0120\LaravelErd;

use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Adapter\DBAL\TableAdapter;
use Recca0120\LaravelErd\Contracts\ColumnAdapterInterface;
use Recca0120\LaravelErd\Contracts\TableAdapterInterface;

class Table
{
    private TableAdapter $table;

    /**
     * @var Collection<int|string, Relation>
     */
    private Collection $relations;

    /**
     * @param  TableAdapterInterface  $table
     * @param  Collection<int|string, Relation>  $relations
     */
    public function __construct(TableAdapterInterface $table, Collection $relations)
    {
        $this->table = $table;
        $this->relations = $relations;
    }

    public function getName(): string
    {
        return $this->table->getName();
    }

    /**
     * @return Collection<int, ColumnAdapterInterface>
     */
    public function getColumns(): Collection
    {
        return $this->table->getColumns();
    }

    /**
     * @return Collection<int, string>
     */
    public function getPrimaryKeys(): Collection
    {
        return $this->table->getPrimaryKeys();
    }

    /**
     * @return Collection<int|string, Relation>
     */
    public function relations(): Collection
    {
        return $this->relations;
    }
}
