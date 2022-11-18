<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Support\Collection;

class Relations
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private Collection $tables;

    /**
     * @param  Collection<Table>  $tables
     */
    public function __construct(Collection $tables)
    {
        $this->tables = $tables;
    }

    /**
     * @return Collection<Relation>
     */
    public function find(): Collection
    {
        return $this->tables
            ->flatMap(fn (Table $table) => $table->columns())
            ->filter(fn (Column $column) => (bool) preg_match('/_id$/', $column->name()))
            ->map(fn (Column $column) => new Relation($column, $this->tables));
    }
}