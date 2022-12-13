<?php

namespace Recca0120\LaravelErdGo\Console\Commands;

use Doctrine\DBAL\Exception;
use Illuminate\Console\Command;
use Recca0120\LaravelErdGo\ErdFinder;
use Recca0120\LaravelErdGo\Templates\ErdGo;

class ErdGoCommand extends Command
{
    protected $signature = 'erd-go {output} {--patterns=\'*.php\'} {--exclude=}';

    /**
     * @throws Exception
     */
    public function handle(ErdFinder $finder, ErdGo $template): int
    {
        $patterns = trim($this->option('patterns'), "\"'");
        $exclude = preg_split('/\s*,\s*/', $this->option('exclude'));
        $result = $finder->find($patterns, $exclude);
        $template->render($result['tables'], $result['relations']);

        return $template->save($this->argument('output'), config('erd-go'));
    }
}