<?php

namespace Recca0120\LaravelErd;

use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Contracts\ColumnSchema;
use Recca0120\LaravelErd\Contracts\TableSchema;

class Table
{
    private TableSchema $tableSchema;

    /**
     * @var Collection<int|string, Relation>
     */
    private Collection $relations;

    public function __construct(TableSchema $table, Collection $relations)
    {
        $this->tableSchema = $table;
        $this->relations = $relations;
    }

    public function name(): string
    {
        return $this->tableSchema->getName();
    }

    /**
     * @return Collection<int, ColumnSchema>
     */
    public function columns(): Collection
    {
        return $this->tableSchema->getColumns();
    }

    public function primaryKeys(): Collection
    {
        return $this->tableSchema->getPrimaryKey();
    }

    /**
     * @return Collection<int|string, Relation>
     */
    public function relations(): Collection
    {
        return $this->relations;
    }
}
