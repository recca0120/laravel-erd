<?php

namespace Recca0120\LaravelErd\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Recca0120\LaravelErd\Factory;
use Recca0120\LaravelErd\Template\Factory as TemplateFactory;
use Symfony\Component\Console\Output\BufferedOutput;
use Throwable;

class GenerateErd extends Command
{
    protected $signature = 'erd:generate {database? : The database connection to use}
        {--directory=} 
        {--file=} 
        {--path=} 
        {--regex=\'*.php\'} 
        {--excludes=}
        {--graceful : Return a successful exit code even if an error occurs}';

    private array $backup = [];

    /**
     * @throws Throwable
     */
    public function handle(Factory $factory, TemplateFactory $templateFactory): int
    {
        $config = config('laravel-erd');
        $database = $this->argument('database');
        $directory = $this->option('directory') ?: app_path();
        $regex = trim($this->option('regex'), "\"'");
        $excludes = preg_split('/\s*,\s*/', $this->option('excludes') ?? '');
        $file = $this->getFile($config, $database);

        try {
            $this->setupFakeDatabase($database);

            if ($this->runMigrate($database) === self::FAILURE) {
                return self::FAILURE;
            }

            $tables = $factory
                ->create($database)
                ->in($directory)
                ->find($regex, $excludes);

            $templateFactory
                ->create($file)
                ->save($tables, $file, $config);

            return self::SUCCESS;
        } catch (Throwable $e) {
            if (! $this->option('graceful')) {
                throw $e;
            }

            $this->error($e->getMessage());

            return self::FAILURE;
        } finally {
            $this->restoreDatabase($database);
        }
    }

    private function runMigrate(?string $database): int
    {
        $default = config('database.default');
        $arguments = array_filter([
            '--database' => $default === $database ? null : $database,
            '--path' => $this->option('path'),
        ]);

        $output = new BufferedOutput;
        if ($this->runCommand('migrate', $arguments, $output) === self::FAILURE) {
            $this->error($output->fetch());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function setupFakeDatabase(?string $database): void
    {
        $default = config('database.default');
        $database = $database ?? $default;
        $connections = config('laravel-erd.connections');

        $this->backup['cache.default'] = config('cache.default');
        $this->backup['database.connections'] = config('database.connections');

        config(['cache.default' => 'array']);
        config(Arr::dot(array_map(static fn (array $config) => $connections[$database] ?? [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => $config['prefix'] ?? '',
            'foreign_key_constraints' => true,
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
        ], $this->backup['database.connections']), 'database.connections.'));

        DB::purge($database);
    }

    private function restoreDatabase(?string $database): void
    {
        $default = config('database.default');
        $arguments = array_filter([
            '--database' => $default === $database ? null : $database,
            '--path' => $this->option('path'),
        ]);

        $output = new BufferedOutput;
        $this->runCommand('migrate:rollback', $arguments, $output);

        DB::purge($database);

        config(['cache.default' => $this->backup['cache.default']]);
        config(['database.connections' => $this->backup['database.connections']]);
        $this->backup = [];
    }

    private function getFile(array $config, ?string $database): string
    {
        $path = $config['storage_path'] ?? storage_path('framework/cache/laravel-erd');
        File::ensureDirectoryExists($path);

        $file = $this->option('file') ?? $database;
        $file = $file ?? config('database.default');
        $file = ! File::extension($file) ? $file.'.'.($config['extension'] ?? 'sql') : $file;

        return $path.'/'.$file;
    }
}
