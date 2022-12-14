<?php

namespace Recca0120\LaravelErd\Templates;

use Doctrine\DBAL\Schema\Column;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Recca0120\LaravelErd\Helpers;
use Recca0120\LaravelErd\Relation;
use Recca0120\LaravelErd\Table;

class DDL implements Template
{
    public function render(Collection $tables): string
    {
        return $tables
            ->map(function (Table $table) {
                return sprintf(
                    "CREATE TABLE %s (\n%s\n)",
                    $table->name(),
                    $this->renderColumn($table)
                );
            })
            ->implode("\n");
    }

    public function save(string $output, string $path, array $options = []): int
    {
        return (int) File::put($path, $output);
    }

    private function renderColumn(Table $table): string
    {
        return $table->columns()
            ->map(function (Column $column) {
                $type = Helpers::getColumnType($column);
                $length = $column->getLength();
                $default = $column->getDefault();
                $comment = $column->getComment();

                return implode(' ', array_filter([
                    $column->getName(),
                    $type . ($length ? "({$length})" : ''),
                    $column->getNotnull() ? 'NOT NULL' : '',
                    $default ? "DEFAULT {$default}" : '',
                    $comment ? "COMMENT {$comment}" : '',
                    $column->getAutoincrement() ? 'AUTO_INCREMENT' : '',
                ]));
            })
            ->merge($this->renderPrimaryKeys($table))
            ->merge($this->renderRelations($table))
            ->filter()
            ->map(fn(string $line) => '    ' . $line)
            ->implode(",\n");
    }

    private function renderRelations(Table $table): Collection
    {
        return $table
            ->relations()
            ->map(function (Relation $relation) {
                $localColumn = Helpers::getColumnName($relation->localKey());
                $foreignTable = Helpers::getTableName($relation->foreignKey());
                $foreignColumn = Helpers::getColumnName($relation->foreignKey());

                return sprintf(
                    'FOREIGN KEY(%s) REFERENCES %s (%s)',
                    $localColumn,
                    $foreignTable,
                    $foreignColumn
                );
            })
            ->unique();
    }

    private function renderPrimaryKeys(Table $table): ?string
    {
        $primaryKeys = (implode(', ', $table->primaryKeys()));

        return $primaryKeys ? "PRIMARY KEY({$primaryKeys})" : null;
    }
}