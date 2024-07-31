<?php

namespace Recca0120\LaravelErd\Templates;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Contracts\ColumnSchema;
use Recca0120\LaravelErd\Helpers;
use Recca0120\LaravelErd\Relation;
use Recca0120\LaravelErd\Table;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
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

    private ExecutableFinder $finder;

    public function __construct()
    {
        $this->finder = new ExecutableFinder();
    }

    public function render(Collection $tables): string
    {
        $results = $tables->map(fn (Table $table): string => $this->renderTable($table));
        $relations = $tables->flatMap(fn (Table $table) => $table->relations());

        return $results->merge(
            $relations
                ->unique(fn (Relation $relation) => $relation->uniqueId())
                ->map(fn (Relation $relationship) => $this->renderRelations($relationship))
                ->sort()
        )->implode("\n");
    }

    public function save(string $output, string $path, array $options = []): int
    {
        $fp = fopen(str_replace('.svg', '.er', $path), 'wb');
        fwrite($fp, $output);

        $meta = stream_get_meta_data($fp);
        $tempFile = $meta['uri'];

        $erdGoBinary = $options['binary']['erd-go'] ?? $this->finder->find('erd-go');
        $dotBinary = $options['binary']['dot'] ?? $this->finder->find('dot');

        $command = sprintf('cat %s | %s | %s -T svg > "%s"', $tempFile, $erdGoBinary, $dotBinary, $path);
        $process = Process::fromShellCommandline($command);

        $process->run();
        $exitCode = $process->wait();

        fclose($fp);

        $errorOutput = $process->getErrorOutput();
        if (! empty($errorOutput)) {
            throw new RuntimeException($errorOutput);
        }

        return $exitCode;
    }

    private function renderTable(Table $table): string
    {
        $primaryKeys = $table->getPrimaryKey();
        $indexes = $table
            ->relations()
            ->filter(fn (Relation $relation) => $relation->type() !== BelongsTo::class)
            ->flatMap(fn (Relation $relation) => [
                Helpers::getColumnName($relation->localKey()),
                Helpers::getColumnName($relation->morphType() ?? ''),
            ])
            ->filter();

        $result = sprintf("[%s] {}\n", $table->getName());
        $result .= $table->getColumns()
            ->map(fn (ColumnSchema $column) => $this->renderColumn($column, $primaryKeys, $indexes))
            ->implode("\n")."\n";

        return $result;
    }

    private function renderColumn(ColumnSchema $column, Collection $primaryKeys, Collection $indexes): string
    {
        return sprintf(
            '%s%s%s {label: "%s, %s"}',
            $primaryKeys->containsStrict($column->getName()) ? '*' : '',
            $indexes->containsStrict($column->getName()) ? '+' : '',
            $column->getName(),
            $column->getType(),
            $column->isNullable() ? 'null' : 'not null'
        );
    }

    private function renderRelations(Relation $relation): string
    {
        return sprintf(
            '%s %s %s',
            Helpers::getTableName($relation->localKey()),
            self::$relations[$relation->type()],
            Helpers::getTableName($relation->foreignKey())
        );
    }
}
