<?php

namespace Recca0120\LaravelErd;

use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Contracts\ColumnSchema;
use Recca0120\LaravelErd\Contracts\TableSchema;

class Table
{
    private TableSchema $schema;

    /**
     * @var Collection<int|string, Relation>
     */
    private Collection $relations;

    public function __construct(TableSchema $schema, Collection $relations)
    {
        $this->schema = $schema;
        $this->relations = $relations;
    }

    public function getName(): string
    {
        return $this->schema->getName();
    }

    /**
     * @return Collection<int, ColumnSchema>
     */
    public function getColumns(): Collection
    {
        return $this->schema->getColumns();
    }

    public function getPrimaryKeys(): Collection
    {
        return $this->schema->getPrimaryKeys();
    }

    /**
     * @return Collection<int|string, Relation>
     */
    public function getRelations(): Collection
    {
        return $this->relations;
    }
}
