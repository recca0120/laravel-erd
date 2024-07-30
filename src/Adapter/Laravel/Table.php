<?php

namespace Recca0120\LaravelErd\Adapter\Laravel;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Adapter\Contracts\Table as TableContract;

class Table implements TableContract
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

    /**
     * @return Collection<int, Column>
     */
    public function getColumns(): Collection
    {
        return collect($this->builder->getColumns($this->name))->map(function (array $column) {
            return new Column($column);
        });
    }

    /**
     * @return Collection<int, string>
     */
    public function getPrimaryKey(): Collection
    {
        return collect($this->builder->getIndexes($this->name))
            ->filter(fn (array $column) => $column['primary'] === true)
            ->map(fn (array $column) => $column['columns'])
            ->collapse();
    }
}
