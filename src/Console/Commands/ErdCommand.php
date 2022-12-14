<?php

namespace Recca0120\LaravelErd\Console\Commands;

use Doctrine\DBAL\Exception;
use Illuminate\Console\Command;
use Recca0120\LaravelErd\ErdFinder;
use Recca0120\LaravelErd\Templates\Er;
use RuntimeException;

class ErdCommand extends Command
{
    protected $signature = 'erd {file} {--patterns=\'*.php\'} {--exclude=} {--directory=}';

    /**
     * @throws Exception
     */
    public function handle(ErdFinder $finder, Er $template): int
    {
        $directory = $this->option('directory') ?? app_path();
        $patterns = trim($this->option('patterns'), "\"'");
        $exclude = preg_split('/\s*,\s*/', $this->option('exclude'));

        try {
            $template->save(
                $template->render($finder->in($directory)->find($patterns, $exclude)),
                $this->argument('file'),
                config('erd-go')
            );

            return self::SUCCESS;
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}