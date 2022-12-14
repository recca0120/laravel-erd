<?php

namespace Recca0120\LaravelErd\Console\Commands;

use Doctrine\DBAL\Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
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
        $file = $this->argument('file');

        try {
            $options = config('laravel-erd.er');
            $storagePath = config('laravel-erd.storage_path') ?? storage_path('framework/cache/laravel-erd');
            File::ensureDirectoryExists($storagePath);

            $template = $factory->supports($file)->create($this->option('template'));
            $output = $template->render($finder->in($directory)->find($patterns, $exclude));

            $template->save($output, $storagePath . '/' . $file, $options);

            return self::SUCCESS;
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}