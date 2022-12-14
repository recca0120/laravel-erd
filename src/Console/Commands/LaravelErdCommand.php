<?php

namespace Recca0120\LaravelErd\Console\Commands;

use Doctrine\DBAL\Exception;
use Illuminate\Console\Command;
use Recca0120\LaravelErd\ErdFinder;
use Recca0120\LaravelErd\Templates\Factory;
use RuntimeException;

class LaravelErdCommand extends Command
{
    protected $signature = 'laravel-erd {file=laravel-erd.ddl} {--patterns=\'*.php\'} {--exclude=} {--directory=} {--template=ddl}';

    /**
     * @throws Exception
     */
    public function handle(ErdFinder $finder, Factory $factory): int
    {
        $directory = $this->option('directory') ?? app_path();
        $patterns = trim($this->option('patterns'), "\"'");
        $exclude = preg_split('/\s*,\s*/', $this->option('exclude'));
        $template = $factory->create($this->option('template'));

        try {
            $storagePath = config('laravel-erd.storage_path') ?? storage_path('framework/cache');

            $template->save(
                $template->render($finder->in($directory)->find($patterns, $exclude)),
                $storagePath . '/' . $this->argument('file'),
                config('laravel-erd.er')
            );

            return self::SUCCESS;
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}