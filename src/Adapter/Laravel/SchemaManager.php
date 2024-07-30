<?php

namespace Recca0120\LaravelErd\Adapter\Laravel;

use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Builder;
use Recca0120\LaravelErd\Adapter\Contracts\SchemaManager as SchemaManagerContract;
use Recca0120\LaravelErd\Adapter\Contracts\Table as TableContract;

class SchemaManager implements SchemaManagerContract
{
    private Builder $schemaBuilder;

    public function __construct(Connection $connection)
    {
        $this->schemaBuilder = $connection->getSchemaBuilder();
    }

    public function introspectTable(string $name): TableContract
    {
        return new Table($this->schemaBuilder, $name);
    }
}
