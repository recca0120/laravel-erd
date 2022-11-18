<?php

namespace Recca0120\LaravelErdGo\Tests;

use Doctrine\DBAL\Schema\Column as DBALColumn;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\TypeRegistry;
use Mockery as m;
use Recca0120\LaravelErdGo\Column;
use Recca0120\LaravelErdGo\Table;

class ColumnTest extends TestCase
{
    private TypeRegistry $typeRegistry;

    public function setUp(): void
    {
        parent::setUp();
        $this->typeRegistry = Type::getTypeRegistry();

    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function test_name(): void
    {
        $column = $this->givenColumn('id', IntegerType::class);

        self::assertEquals('id', $column->name());
    }

    /**
     * @dataProvider typesProvider
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function test_type($expected, $type): void
    {
        $column = $this->givenColumn('id', $type);

        self::assertEquals($expected, $column->type());
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function test_nullable(): void
    {
        $column = $this->givenColumn('id', IntegerType::class, [
            'notnull' => false,
        ]);

        self::assertTrue($column->nullable());
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function test_default(): void
    {
        $column = $this->givenColumn('id', IntegerType::class, [
            'default' => 10,
        ]);

        self::assertEquals(10, $column->default());
    }

    public function typesProvider(): array
    {
        return collect(Type::getTypesMap())->map(fn ($value, $key) => [$key, $value])->values()->toArray();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function getType(string $type): Type
    {
        $map = array_flip(Type::getTypesMap());

        return $this->typeRegistry->get($map[$type]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function givenColumn(string $name, string $type, array $options = []): Column
    {
        return new Column(
            new DBALColumn($name, $this->getType($type), $options),
            m::mock(Table::class)
        );
    }
}