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
            ->map(fn (Table $table) => sprintf(
                "CREATE TABLE %s (\n%s\n)",
                $table->name(),
                $this->renderColumn($table)
            ))
            ->merge($this->renderRelations($tables))
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
                $type = $this->getColumnType($column);
                $precision = $column->getPrecision();
                $default = $column->getDefault();
                $comment = $column->getComment();

                return implode(' ', array_filter([
                    $column->getName(),
                    $type.($precision ? "({$precision})" : ''),
                    $column->getNotnull() ? 'NOT NULL' : '',
                    $default ? "DEFAULT {$default}" : '',
                    $comment ? "COMMENT {$comment}" : '',
                    $column->getAutoincrement() ? 'AUTO_INCREMENT' : '',
                ]));
            })
            ->merge($this->renderPrimaryKeys($table))
            ->filter()
            ->map(fn (string $line) => '    '.$line)
            ->implode(",\n");
    }

    /**
     * @param  Table  $table
     * @return string[]
     */
    private function renderPrimaryKeys(Table $table): array
    {
        $primaryKeys = (implode(', ', $table->primaryKeys()));

        return $primaryKeys ? ["PRIMARY KEY({$primaryKeys})"] : [];
    }

    /**
     * @param  Collection<string, Table>  $tables
     * @return Collection<string, string>
     */
    private function renderRelations(Collection $tables): Collection
    {
        return $tables
            ->map(fn (Table $table) => $table->relations())
            ->collapse()
            ->unique(fn (Relation $relation) => $relation->uniqueId())
            ->map(fn (Relation $relation) => $this->renderRelation($relation))
            ->sort();
    }

    private function renderRelation(Relation $relation): string
    {
        $localTable = Helpers::getTableName($relation->localKey());
        $foreignTable = Helpers::getTableName($relation->foreignKey());
        $localColumn = Helpers::getColumnName($relation->localKey());
        $foreignColumn = Helpers::getColumnName($relation->foreignKey());

        return sprintf(
            'ALTER TABLE %s ADD FOREIGN KEY (%s) REFERENCES %s (%s)',
            $localTable,
            $localColumn,
            $foreignTable,
            $foreignColumn
        );
    }

    private function getColumnType(Column $column): string
    {
        $type = Helpers::getColumnType($column);

        return $type === 'string' ? 'varchar' : $type;
    }
}
