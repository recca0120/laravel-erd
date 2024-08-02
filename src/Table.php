<?php

namespace Recca0120\LaravelErd;

use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Adapter\TableAdapter;

class Table
{
    private TableAdapter $table;

    /**
     * @var Collection<int|string, Relation>
     */
    private Collection $relations;

    /**
     * @param  TableAdapter  $table
     * @param  Collection<int|string, Relation>  $relations
     */
    public function __construct(TableAdapter $table, Collection $relations)
    {
        $this->table = $table;
        $this->relations = $relations;
    }

    public function getName(): string
    {
        return $this->table->getName();
    }

    public function getColumns(): Collection
    {
        return $this->table->getColumns();
    }

    /**
     * @return string[]
     */
    public function getPrimaryKeys(): array
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
