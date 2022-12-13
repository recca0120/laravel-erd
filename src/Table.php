<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Schema\Column as DBALColumn;
use Doctrine\DBAL\Schema\Table as DBALTable;
use Illuminate\Support\Collection;

class Table
{
    private DBALTable $table;
    /**
     * @var Collection<DBALColumn>
     */
    private Collection $relations;

    public function __construct(DBALTable $table, Collection $relations)
    {
        $this->table = $table;
        $this->relations = $relations;
    }

    public function name(): string
    {
        return $this->table->getName();
    }

    public function columns(): Collection
    {
        return collect($this->table->getColumns());
    }

    public function relations(): Collection
    {
        return $this->relations;
    }
}