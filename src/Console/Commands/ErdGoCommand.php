<?php

namespace Recca0120\LaravelErdGo\Console\Commands;

use Doctrine\DBAL\Exception;
use Illuminate\Console\Command;
use Recca0120\LaravelErdGo\ErdFinder;
use Recca0120\LaravelErdGo\Templates\ErdGo;
use RuntimeException;

class ErdGoCommand extends Command
{
    protected $signature = 'erd-go {file} {--patterns=\'*.php\'} {--exclude=} {--directory=}';

    /**
     * @throws Exception
     */
    public function handle(ErdFinder $finder, ErdGo $template): int
    {
        $directory = $this->option('directory') ?? app_path();
        $patterns = trim($this->option('patterns'), "\"'");
        $exclude = preg_split('/\s*,\s*/', $this->option('exclude'));
        $template->render($finder->in($directory)->find($patterns, $exclude));

        try {
            $template->save($this->argument('file'), config('erd-go'));

            return self::SUCCESS;
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}