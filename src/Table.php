<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Schema\Column as DBALColumn;

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

    public function name(): string
    {
        return $this->name;
    }

    public function columns(): array
    {
        return $this->columns;
    }
}