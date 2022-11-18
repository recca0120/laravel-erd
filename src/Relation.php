<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Relation
{
    private Column $column;
    /**
     * @var Collection<Table>
     */
    private Collection $tables;

    public function __construct(Column $column, Collection $tables)
    {
        $this->column = $column;
        $this->tables = $tables;
    }

    public function left(): string
    {
        $tableLookup = $this->tables->map(fn (Table $table) => $table->name());
        $columnName = $this->column->name();
        $pos = strrpos($columnName, '_');
        $left = substr($columnName, 0, $pos);
        $plural = Str::plural($left);
        $find = $tableLookup->contains($plural);

        return $find ? $plural : Str::singular($left);
    }

    public function right(): string
    {
        return $this->column->table()->name();
    }

    public function operator(): string
    {
        $left = $this->left();
        $isPlural = Str::plural($left) === $left;

        return $isPlural ? '1--*' : '1--1';
    }

    public function render(): string
    {
        $tableLookup = $this->tables->map(fn (Table $table) => $table->name());
        $columnName = $this->column->name();
        $pos = strrpos($columnName, '_');
        $left = substr($columnName, 0, $pos);
        $plural = Str::plural($left);
        $isPlural = $tableLookup->contains($plural);
        $left = $isPlural ? $plural : Str::singular($left);
        $right = $this->column->table()->name();
        $operator = $isPlural ? '1--*' : '1--1';

        return sprintf('%s %s %s', $left, $operator, $right);
    }
}