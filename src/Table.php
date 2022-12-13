<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Schema\Column as DBALColumn;
use Illuminate\Support\Collection;

class Table
{
    private string $name;
    /**
     * @var Collection<DBALColumn>
     */
    private Collection $columns;
    private Collection $relations;

    public function __construct(string $name, Collection $columns, Collection $relations)
    {
        $this->name = $name;
        $this->columns = $columns;
        $this->relations = $relations;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function columns(): Collection
    {
        return $this->columns;
    }

    public function relations(): Collection
    {
        return $this->relations;
    }
}