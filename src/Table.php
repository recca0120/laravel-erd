<?php

namespace Recca0120\LaravelErdGo;

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
        return Collection::make($this->table->getColumns())->mapInto(Column::class);
    }

    public function render(): string
    {
        return sprintf('[%s] {}', $this->name());
    }
}