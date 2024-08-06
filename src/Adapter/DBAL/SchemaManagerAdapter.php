<?php

namespace Recca0120\LaravelErd\Adapter\DBAL;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Recca0120\LaravelErd\Contracts\SchemaManagerAdapterInterface;
use Recca0120\LaravelErd\Contracts\TableAdapterInterface;

class SchemaManagerAdapter implements SchemaManagerAdapterInterface
{
    private AbstractSchemaManager $schemaManager;

    public function __construct(AbstractSchemaManager $schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

    /**
     * @throws Exception
     */
    public function introspectTable(string $table): TableAdapterInterface
    {
        return new TableAdapter($this->schemaManager->introspectTable($table));
    }
}