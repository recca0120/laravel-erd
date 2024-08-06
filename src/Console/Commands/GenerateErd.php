<?php

namespace Recca0120\LaravelErd\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Recca0120\LaravelErd\Factory;
use Recca0120\LaravelErd\Template\Factory as TemplateFactory;
use Throwable;

class GenerateErd extends Command
{
    protected $signature = 'erd:generate {file=laravel-erd} {--patterns=\'*.php\'} {--exclude=} {--directory=} {--database=laravel-erd}';

    public function handle(Factory $factory, TemplateFactory $templateFactory): int
    {
        $this->setConnection($this->option('database'));

        if (Artisan::call('migrate') === self::FAILURE) {
            $this->error(Artisan::output());

            return self::FAILURE;
        }

        $config = config('laravel-erd');
        $directory = $this->option('directory') ?? app_path();
        $patterns = trim($this->option('patterns'), "\"'");
        $exclude = preg_split('/\s*,\s*/', $this->option('exclude') ?? '');
        $file = $this->argument('file');
        $file = ! File::extension($file) ? $file.'.'.($config['extension'] ?? 'sql') : $file;

        try {
            $erdFinder = $factory->create();
            $output = $templateFactory->create($file)->render(
                $erdFinder->in($directory)->find($patterns, $exclude)
            );

            $options = config('laravel-erd');
            $storagePath = $options['storage_path'] ?? storage_path('framework/cache/laravel-erd');
            File::ensureDirectoryExists($storagePath);

            $templateFactory->create($file)->save($output, $storagePath.'/'.$file, $options);

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    private function setConnection(string $connection): void
    {
        config(
            collect(Arr::dot(config()->all()))
                ->filter(fn ($value, $key) => $value && Str::endsWith($key, 'database.connection'))
                ->map(fn () => $connection)
                ->merge(['database.default' => $connection])
                ->toArray()
        );
    }
}
