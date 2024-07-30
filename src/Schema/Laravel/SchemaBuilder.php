<?php

namespace Recca0120\LaravelErd\Schema\Laravel;

use Illuminate\Database\Schema\Builder;
use Recca0120\LaravelErd\Contracts\SchemaBuilder as SchemaBuilderContract;
use Recca0120\LaravelErd\Contracts\TableSchema as TableSchemaContract;

class SchemaBuilder implements SchemaBuilderContract
{
    private Builder $builder;

    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    public function getTableSchema(string $name): TableSchemaContract
    {
        return new TableSchema($this->builder, $name);
    }
}
