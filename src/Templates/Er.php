<?php

namespace Recca0120\LaravelErd\Templates;

use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Helpers;
use Recca0120\LaravelErd\Relation;
use Recca0120\LaravelErd\Table;
use RuntimeException;
use Symfony\Component\Process\Process;

class Er implements Template
{
    /** @var string[] */
    private static array $relations = [
        BelongsTo::class => '1--1',
        HasOne::class => '1--1',
        MorphOne::class => '1--1',
        HasMany::class => '1--*',
        MorphMany::class => '1--*',
        BelongsToMany::class => '*--*',
        MorphToMany::class => '*--*',
    ];

    public function render(Collection $tables): string
    {
        $results = $tables->map(fn(Table $table): string => $this->renderTable($table));
        $relations = $tables->flatMap(fn(Table $table) => $table->relations());

        return $results->merge(
            $relations
                ->unique(fn(Relation $relation) => $relation->uniqueId())
                ->sortBy(fn(Relation $relation) => $relation->order())
                ->map(fn(Relation $relationship) => $this->renderRelations($relationship))
                ->sort()
        )->implode("\n");
    }

    public function save(string $output, string $path, array $options = []): int
    {
        $fp = tmpfile();
        fwrite($fp, $output);

        $meta = stream_get_meta_data($fp);
        $tempFile = $meta['uri'];

        $erdGoBinary = $options['erd-go'] ?? '/usr/local/bin/erd-go';
        $dotBinary = $options['dot'] ?? '/usr/local/bin/dot';

        $command = sprintf('cat %s | %s | %s -T svg > "%s"', $tempFile, $erdGoBinary, $dotBinary, $path);
        $process = Process::fromShellCommandline($command);

        $process->run();
        $exitCode = $process->wait();

        fclose($fp);

        $errorOutput = $process->getErrorOutput();
        if (!empty($errorOutput)) {
            throw new RuntimeException($errorOutput);
        }

        return $exitCode;
    }

    private function renderTable(Table $table): string
    {
        $primaryKeys = $table->primaryKeys();
        $indexes = $table
            ->relations()
            ->filter(fn(Relation $relation) => $relation->type() !== BelongsTo::class)
            ->flatMap(fn(Relation $relation) => [
                Helpers::getColumnName($relation->localKey()),
                Helpers::getColumnName($relation->morphType()),
            ])
            ->filter()
            ->toArray();

        $result = sprintf("[%s] {}\n", $table->name());
        $result .= $table->columns()
                ->map(fn(Column $column) => $this->renderColumn($column, $primaryKeys, $indexes))
                ->implode("\n") . "\n";

        return $result;
    }

    /**
     * @param Column $column
     * @param array<int, string> $primaryKeys
     * @param array<int, string> $indexes
     * @return string
     */
    private function renderColumn(Column $column, array $primaryKeys, array $indexes): string
    {
        return sprintf(
            '%s%s%s {label: "%s, %s"}',
            in_array($column->getName(), $primaryKeys, true) ? '*' : '',
            in_array($column->getName(), $indexes, true) ? '+' : '',
            $column->getName(),
            Helpers::getColumnType($column),
            $column->getNotnull() ? 'not null' : 'null'
        );
    }

    private function renderRelations(Relation $relations): string
    {
        return sprintf(
            '%s %s %s',
            Helpers::getTableName($relations->localKey()),
            self::$relations[$relations->type()],
            Helpers::getTableName($relations->foreignKey())
        );
    }
}