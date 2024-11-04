<?php

namespace Recca0120\LaravelErd\Schema\Laravel;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Contracts\TableSchema as TableSchemaContract;

class TableSchema implements TableSchemaContract
{
    private Builder $builder;

    private string $name;

    public function __construct(Builder $builder, string $name)
    {
        $this->builder = $builder;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColumns(): Collection
    {
        return collect($this->builder->getColumns($this->name))->map(fn (array $column) => new ColumnSchema($column));
    }

    public function getPrimaryKeys(): Collection
    {
        return collect($this->builder->getIndexes($this->name))
            ->filter(fn (array $column) => $column['primary'] === true)
            ->map(fn (array $column) => $column['columns'])
            ->collapse();
    }
}
