<?php

namespace Recca0120\LaravelErd\Template;

use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Contracts\ColumnSchema;
use Recca0120\LaravelErd\Relation;
use Recca0120\LaravelErd\Table;

class DDL implements Template
{
    public function render(Collection $tables): string
    {
        return $tables
            ->map(fn (Table $table) => sprintf(
                "CREATE TABLE %s (\n%s\n)",
                $table->getName(),
                $this->renderColumn($table)
            ))
            ->merge($this->renderRelations($tables))
            ->implode("\n");
    }

    public function save(Collection $tables, string $path, array $options = []): int
    {
        return (int) file_put_contents($path, $this->render($tables));
    }

    private function renderColumn(Table $table): string
    {
        return $table->getColumns()
            ->map(function (ColumnSchema $column) {
                $type = $column->getType();
                $precision = $column->getPrecision();
                $default = $column->getDefault();
                $comment = $column->getComment();

                return implode(' ', array_filter([
                    $column->getName(),
                    $type.($precision ? "({$precision})" : ''),
                    $column->isNullable() ? '' : 'NOT NULL',
                    $default ? "DEFAULT {$default}" : '',
                    $comment ? "COMMENT {$comment}" : '',
                    $column->isAutoIncrement() ? 'AUTO_INCREMENT' : '',
                ]));
            })
            ->merge($this->renderPrimaryKeys($table))
            ->filter()
            ->map(fn (string $line) => '    '.$line)
            ->implode(",\n");
    }

    /**
     * @return string[]
     */
    private function renderPrimaryKeys(Table $table): array
    {
        $primaryKeys = $table->getPrimaryKeys()->implode(', ');

        return $primaryKeys ? ["PRIMARY KEY({$primaryKeys})"] : [];
    }

    /**
     * @param  Collection<string, Table>  $tables
     * @return Collection<string, string>
     */
    private function renderRelations(Collection $tables): Collection
    {
        return $tables
            ->map(fn (Table $table) => $table->getRelations())
            ->collapse()
            ->unique(fn (Relation $relation) => $relation->uniqueId())
            ->map(fn (Relation $relation) => $this->renderRelation($relation))
            ->sort();
    }

    private function renderRelation(Relation $relation): string
    {
        return sprintf(
            'ALTER TABLE %s ADD FOREIGN KEY (%s) REFERENCES %s (%s)',
            $relation->localTable(),
            $relation->localColumn(),
            $relation->foreignTable(),
            $relation->foreignColumn()
        );
    }
}
