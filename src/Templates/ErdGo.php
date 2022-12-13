<?php

namespace Recca0120\LaravelErdGo\Templates;

use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Recca0120\LaravelErdGo\Helpers;
use Recca0120\LaravelErdGo\Relation;
use Recca0120\LaravelErdGo\Table;
use Symfony\Component\Process\Process;

class ErdGo
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
    private string $output;

    public function render(Collection $tables): string
    {
        $results = $tables->map(fn(Table $table): string => $this->renderTable($table));
        $relationships = $tables->flatMap(fn(Table $table) => $table->relations());

        return $this->output = $results->merge(
            $relationships
                ->unique(fn(Relation $relationship) => $relationship->uniqueId())
                ->sortBy(fn(Relation $relationship) => $relationship->sortBy())
                ->map(fn(Relation $relationship) => $this->renderRelations($relationship))
                ->sort()
        )->implode("\n");
    }

    public function save(string $path, array $options = []): int
    {
        $fp = tmpfile();
        fwrite($fp, $this->output);

        $meta = stream_get_meta_data($fp);
        $tempFile = $meta['uri'];

        $erdGoBinary = $options['erd-go'] ?? '/usr/local/bin/erd-go';
        $dotBinary = $options['dot'] ?? '/usr/local/bin/dot';

        $command = sprintf('cat %s | %s | %s -T png -o %s', $tempFile, $erdGoBinary, $dotBinary, $path);
        $process = Process::fromShellCommandline($command);

        $process->start();
        $exitCode = $process->wait();

        fclose($fp);

        return $exitCode;
    }

    private function renderTable(Table $table): string
    {
        $result = sprintf("[%s] {}\n", $table->name());
        $result .= $table->columns()
                ->map(fn(Column $column) => $this->renderColumn($column))
                ->implode("\n") . "\n";

        return $result;
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