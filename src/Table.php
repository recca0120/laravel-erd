<?php

namespace Recca0120\LaravelErdGo;

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

    public function name(): string
    {
        return $this->table->getName();
    }

    public function columns(): Collection
    {
        return collect($this->table->getColumns())->map(fn (DBALColumn $column) => new Column($column, $this));
    }

    public function render(): string
    {
        return sprintf('[%s] {}', $this->name());
    }
}