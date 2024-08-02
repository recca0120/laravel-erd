<?php

namespace Recca0120\LaravelErd\Adapter;

use Doctrine\DBAL\Schema\AbstractSchemaManager;

class SchemaManagerAdapter
{

    private AbstractSchemaManager $schemaManager;

    public function __construct(AbstractSchemaManager $schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

    public function introspectTable(string $table)
    {
        return $this->schemaManager->introspectTable($table);
    }
}