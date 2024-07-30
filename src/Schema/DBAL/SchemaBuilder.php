<?php

namespace Recca0120\LaravelErd\Schema\DBAL;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Recca0120\LaravelErd\Contracts\SchemaBuilder as SchemaBuilderContract;
use Recca0120\LaravelErd\Contracts\TableSchema as TableSchemaContract;

class SchemaBuilder implements SchemaBuilderContract
{
    private AbstractSchemaManager $schemaManager;

    public function __construct(AbstractSchemaManager $schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

    public function getTableSchema(string $name): TableSchemaContract
    {
        return new TableSchema($this->schemaManager->introspectTable($name));
    }
}
