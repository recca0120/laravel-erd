<?php

namespace Recca0120\LaravelErd;

use Doctrine\DBAL\Schema\Column as DBALColumn;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Adapter\Table as TableAdapter;

class Table
{
    private TableAdapter $table;

    /**
     * @var Collection<int|string, Relation>
     */
    private Collection $relations;

    public function __construct(TableAdapter $table, Collection $relations)
    {
        $this->table = $table;
        $this->relations = $relations;
    }

    public function name(): string
    {
        return $this->table->getName();
    }

    /**
     * @return Collection<int, DBALColumn>
     */
    public function columns(): Collection
    {
        return $this->table->getColumns();
    }

    public function primaryKeys(): Collection
    {
        return $this->table->getPrimaryKey();
    }

    /**
     * @return Collection<int|string, Relation>
     */
    public function relations(): Collection
    {
        return $this->relations;
    }
}
