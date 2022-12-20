<?php

namespace Recca0120\LaravelErd\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Recca0120\LaravelErd\ErdFinder;
use Recca0120\LaravelErd\Templates\Factory;
use RuntimeException;

class LaravelErdCommand extends Command
{
    protected $signature = 'laravel-erd {file=laravel-erd.sql} {--patterns=\'*.php\'} {--exclude=} {--directory=} {--template=ddl}';

    public function handle(ErdFinder $finder, Factory $factory): int
    {
        $directory = $this->option('directory') ?? app_path();
        $patterns = trim($this->option('patterns'), "\"'");
        $exclude = preg_split('/\s*,\s*/', $this->option('exclude') ?? '');
        $file = $this->argument('file');

        try {
            $template = $factory->allowFileExtension($file)->create($this->option('template'));
            $output = $template->render($finder->in($directory)->find($patterns, $exclude));

            $options = config('laravel-erd');
            $storagePath = $options['storage_path'] ?? storage_path('framework/cache/laravel-erd');
            File::ensureDirectoryExists($storagePath);

            $template->save($output, $storagePath . '/' . $file, $options);

            return self::SUCCESS;
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}