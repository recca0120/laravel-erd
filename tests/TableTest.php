<?php

namespace Recca0120\LaravelErdGo\Tests;

use Doctrine\DBAL\Schema\Column as DBALColumn;
use Doctrine\DBAL\Schema\Table as DBALTable;
use Doctrine\DBAL\Types\Type;
use Recca0120\LaravelErdGo\Column;
use Recca0120\LaravelErdGo\Table;

class TableTest extends TestCase
{
    private Table $table;

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $typeRegistry = Type::getTypeRegistry();
        $this->table = new Table(new DBALTable('users', [
            new DBALColumn('id', $typeRegistry->get('integer')),
        ]));
    }

    public function test_get_name(): void
    {
        self::assertEquals('users', $this->table->name());
    }

    public function test_get_column(): void
    {
        self::assertInstanceOf(Column::class, $this->table->columns()->first());
    }

    public function test_to_string(): void
    {
        self::assertEquals('[users] {}', $this->table->render());
    }
}