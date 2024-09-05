<?php

namespace Recca0120\LaravelErd\Template;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Contracts\ColumnSchema;
use Recca0120\LaravelErd\Relation;
use Recca0120\LaravelErd\Table;
use RuntimeException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class Er implements Template
{
    /** @var string[] */
    private static array $relations = [
        BelongsTo::class => '1--*',
        MorphTo::class => '1--*',
        HasOne::class => '1--1',
        MorphOne::class => '1--1',
        HasMany::class => '1--*',
        MorphMany::class => '1--*',
        BelongsToMany::class => '1--*',
        MorphToMany::class => '1--*',
    ];

    private ExecutableFinder $finder;

    public function __construct()
    {
        $this->finder = new ExecutableFinder();
    }

    public function render(Collection $tables): string
    {
        $results = $tables->map(fn (Table $table): string => $this->renderTable($table));
        $relations = $tables->flatMap(fn (Table $table) => $table->getRelations());

        return $results->merge(
            $relations
                ->unique(fn (Relation $relation) => $relation->uniqueId())
                ->map(fn (Relation $relationship) => $this->renderRelation($relationship))
                ->sort()
        )->implode("\n");
    }

    public function save(Collection $tables, string $path, array $options = []): int
    {
        $fp = fopen(str_replace('.svg', '.er', $path), 'wb');
        fwrite($fp, $this->render($tables));
        $meta = stream_get_meta_data($fp);
        fclose($fp);

        $process = Process::fromShellCommandline($this->getCommand($options, $meta['uri'], $path));

        $process->run();
        $exitCode = $process->wait();

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
            ->getRelations()
            ->flatMap(fn (Relation $relation) => [$relation->localColumn(), $relation->morphColumn()])
            ->filter();

        return $table->getColumns()
                ->map(fn (ColumnSchema $column) => $this->renderColumn($column, $primaryKeys, $indexes))
                ->prepend(sprintf('[%s] {}', $table->getName()))
                ->implode("\n")."\n";
    }

    private function renderColumn(ColumnSchema $column, Collection $primaryKeys, Collection $indexes): string
    {
        $type = $column->getType();

        return sprintf(
            '%s%s%s {label: "%s, %s"}',
            $primaryKeys->containsStrict($column->getName()) ? '*' : '',
            $indexes->containsStrict($column->getName()) ? '+' : '',
            $column->getName(),
            $type === 'varchar' ? 'string' : $type,
            $column->isNullable() ? 'null' : 'not null'
        );
    }

    private function renderRelation(Relation $relation): string
    {
        return sprintf(
            '%s %s %s',
            $relation->localTable(),
            self::$relations[$relation->type()],
            $relation->foreignTable()
        );
    }

    private function getCommand($option, string $uri, string $path): string
    {
        $binary = $option['binary'];
        $erdGo = is_executable($binary['erd-go']) ? $binary['erd-go'] : $this->finder->find('erd-go');
        $dot = is_executable($binary['dot']) ? $binary['dot'] : $this->finder->find('dot');

        return sprintf('cat %s | %s | %s -T svg > "%s"', $uri, $erdGo, $dot, $path);
    }
}
