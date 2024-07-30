<?php

namespace Recca0120\LaravelErd\Adapter;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Container\Container;

class SchemaManager
{
    private AbstractSchemaManager $schemaManager;

    public function __construct(Container $container)
    {
        $this->schemaManager = $container->make('db')->getDoctrineSchemaManager();
    }

    public function introspectTable(string $name)
    {
        return new Table($this->schemaManager->introspectTable($name));
    }
}
