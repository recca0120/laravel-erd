<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Table as DBALTable;
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
     */
    public function generate(array $filtered = [])
    {
        Collection::make($this->schemaManager->listTables())
            ->when($filtered, function ($tables) use ($filtered) {
                return $tables->filter(fn (DBALTable $table) => in_array($table->getName(), $filtered, true));
            })
            ->mapInto(Table::class)
            ->each(function (Table $table) {
                echo $table->render()."\n";
                $table->columns()->each(function (Column $column) {
                    echo $column->render()."\n";
                });
                echo "\n";
            });
    }
}