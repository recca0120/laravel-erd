<?php

namespace Recca0120\LaravelErd\Adapter\DBAL;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Database\Connection;
use Recca0120\LaravelErd\Adapter\Contracts\SchemaManager as SchemaManagerContract;
use Recca0120\LaravelErd\Adapter\Contracts\Table as TableContract;

class SchemaManager implements SchemaManagerContract
{
    private AbstractSchemaManager $schemaManager;

    public function __construct(Connection $connection)
    {
        $this->schemaManager = $connection->getDoctrineSchemaManager();
    }

    public function introspectTable(string $name): TableContract
    {
        return new Table($this->schemaManager->introspectTable($name));
    }
}
