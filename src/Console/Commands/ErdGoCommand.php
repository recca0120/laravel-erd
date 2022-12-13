<?php

namespace Recca0120\LaravelErdGo\Console\Commands;

use Doctrine\DBAL\Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Recca0120\LaravelErdGo\ErdFinder;
use Recca0120\LaravelErdGo\Template;

class ErdGoCommand extends Command
{
    protected $signature = 'erd-go {outputFile} {--patterns=\'*.php\'} {--exclude=}';

    /**
     * @throws Exception
     */
    public function handle(ErdFinder $finder): int
    {
        $outputFile = $this->argument('outputFile');
        $patterns = trim($this->option('patterns'), "\"'");
        $exclude = preg_split('/\s*,\s*/', $this->option('exclude'));
        $template = new Template();
        $result = $finder->find($patterns, $exclude);

        File::put(
            base_path($outputFile),
            $template->render($result['tables'], $result['relationships'])
        );

        return self::SUCCESS;
    }
}