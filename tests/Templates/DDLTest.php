<?php

namespace Recca0120\LaravelErd\Tests\Templates;

use Doctrine\DBAL\Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\ErdFinder;
use Recca0120\LaravelErd\Templates\DDL;
use Recca0120\LaravelErd\Templates\Template;
use Recca0120\LaravelErd\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class DDLTest extends TestCase
{
    use RefreshDatabase;
    use MatchesSnapshots;

    private Template $template;

    protected function setUp(): void
    {
        parent::setUp();
        $this->template = new DDL();
    }

    /**
     * @throws Exception
     */
    public function test_find_er_model_in_directory(): void
    {
        $finder = $this->givenFinder();

        $this->assertMatchesSnapshot(
            $this->render($finder->find())
        );
    }

    private function givenFinder(): ErdFinder
    {
        return $this->app->make(ErdFinder::class)->in(__DIR__ . '/../fixtures');
    }

    private function render(Collection $results): string
    {
        return $this->template->render($results);
    }
}
