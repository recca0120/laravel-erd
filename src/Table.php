<?php

namespace Recca0120\LaravelErd;

use Doctrine\DBAL\Schema\Column as DBALColumn;
use Doctrine\DBAL\Schema\Table as DBALTable;
use Illuminate\Support\Collection;

class Table
{
    private DBALTable $table;

    /**
     * @var Collection<int|string, Relation>
     */
    private Collection $relations;

    /**
     * @param  DBALTable  $table
     * @param  Collection<int|string, Relation>  $relations
     */
    public function __construct(DBALTable $table, Collection $relations)
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
        return collect($this->table->getColumns());
    }

    /**
     * @return string[]
     */
    public function primaryKeys(): array
    {
        $primaryKey = $this->table->getPrimaryKey();

        return $primaryKey ? $primaryKey->getColumns() : [];
    }

    /**
     * @return Collection<int|string, Relation>
     */
    public function relations(): Collection
    {
        return $this->relations;
    }
}
