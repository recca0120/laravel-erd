<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Column as DBALColumn;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\TypeRegistry;

class Table
{
    private string $name;
    /**
     * @var DBALColumn[]
     */
    private array $columns;

    /**
     * @param DBALColumn[] $columns
     */
    public function __construct(string $name, array $columns)
    {
        $this->name = $name;
        $this->columns = $columns;
    }

    public function render(): string
    {
        $result = sprintf("[%s] {}\n", $this->name);
        $result .= collect($this->columns)
                ->map(function (Column $column) {
                    return sprintf(
                        '%s {label: "%s, %s"}',
                        $column->getName(),
                        $this->getColumnType($column),
                        $column->getNotnull() ? 'not null' : 'null'
                    );
                })
                ->implode("\n") . "\n";

        return $result;
    }

    private function getColumnType(Column $column): string
    {
        try {
            return $this->getTypeRegistry()->lookupName($column->getType());
        } catch (Exception $e) {
            return 'unknown';
        }
    }

    private function getTypeRegistry(): TypeRegistry
    {
        return Type::getTypeRegistry();
    }
}