<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

class Template
{
    /** @var string[] */
    private static array $relationships = [
        BelongsTo::class => '1--1',
        HasOne::class => '1--1',
        MorphOne::class => '1--1',
        HasMany::class => '1--*',
        MorphMany::class => '1--*',
        BelongsToMany::class => '*--*',
        MorphToMany::class => '*--*',
    ];

    public function render(Collection $tables, Collection $relationships): string
    {
        $results = $tables->map(fn(Table $table): string => $this->renderTable($table));

        return $results->merge(
            $relationships
                ->unique(fn(Relationship $relationship) => $relationship->uniqueId())
                ->sortBy(fn(Relationship $relationship) => $relationship->sortBy())
                ->map(fn(Relationship $relationship) => $this->renderRelationship($relationship))
                ->sort()
        )->implode("\n");
    }

    public function renderTable(Table $table): string
    {
        $result = sprintf("[%s] {}\n", $table->name());
        $result .= collect($table->columns())
                ->map(fn(Column $column) => $this->renderColumn($column))
                ->implode("\n") . "\n";

        return $result;
    }

    private function renderRelationship(Relationship $relationship): string
    {
        return sprintf(
            '%s %s %s',
            Helpers::getTableName($relationship->localKey()),
            self::$relationships[$relationship->type()],
            Helpers::getTableName($relationship->foreignKey())
        );
    }

    private function renderColumn(Column $column): string
    {
        return sprintf(
            '%s {label: "%s, %s"}',
            $column->getName(),
            Helpers::getColumnType($column),
            $column->getNotnull() ? 'not null' : 'null'
        );
    }

}