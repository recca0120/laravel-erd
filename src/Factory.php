<?php

namespace Recca0120\LaravelErd;

use Illuminate\Database\ConnectionResolverInterface;
use Recca0120\LaravelErd\Schema\DBAL\SchemaBuilder as DBALSchemaBuilder;
use Recca0120\LaravelErd\Schema\Laravel\SchemaBuilder as LaravelSchemaBuilder;

class Factory
{
    private array $cache = [];

    private ConnectionResolverInterface $connectionResolver;

    public function __construct(ConnectionResolverInterface $connectionResolver)
    {
        $this->connectionResolver = $connectionResolver;
    }

    public function create(?string $name = null): ErdFinder
    {
        $key = $name ?: 'default';

        if (! empty($this->cache[$key])) {
            return $this->cache[$key];
        }

        return $this->cache[$key] = new ErdFinder(
            $this->getSchemaBuilder($name), new ModelFinder(), new RelationFinder()
        );
    }

    /**
     * @return DBALSchemaBuilder|LaravelSchemaBuilder
     */
    private function getSchemaBuilder(?string $name)
    {
        $connection = $this->connectionResolver->connection($name);

        return method_exists($connection, 'getDoctrineSchemaManager')
            ? new DBALSchemaBuilder($connection->getDoctrineSchemaManager())
            : new LaravelSchemaBuilder($connection->getSchemaBuilder());
    }
}
