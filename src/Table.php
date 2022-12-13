<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Schema\Column as DBALColumn;
use Illuminate\Support\Collection;

class Table
{
    private string $name;
    /**
     * @var DBALColumn[]
     */
    private array $columns;
    private Collection $relations;

    /**
     * @param  DBALColumn[]  $columns
     */
    public function __construct(string $name, array $columns, Collection $relations)
    {
        $this->name = $name;
        $this->columns = $columns;
        $this->relations = $relations;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function columns(): array
    {
        return $this->columns;
    }

    public function relations(): Collection
    {
        return $this->relations;
    }
}