<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Table as DBALTable;
use eftec\bladeone\BladeOne;
use Illuminate\Support\Collection;

class ErdGo
{
    private AbstractSchemaManager $schemaManager;

    public function __construct(AbstractSchemaManager $schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

    /**
     * @throws DBALException
     * @throws \Exception
     */
    public function generate(array $filtered = [])
    {
        /** @var Collection $tables */
        $tables = Collection::make($this->schemaManager->listTables())
            ->when($filtered, function ($tables) use ($filtered) {
                return $tables->filter(fn (DBALTable $table) => in_array($table->getName(), $filtered, true));
            })
            ->mapInto(Table::class);

        $relations = (new Relations($tables))->find();

        $blade = new BladeOne(__DIR__.'/../views', __DIR__.'/../compiles', BladeOne::MODE_DEBUG);

        return $blade->run('erd-go', compact('tables', 'relations'));
    }
}