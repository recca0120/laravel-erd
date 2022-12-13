<?php

namespace Recca0120\LaravelErdGo\Tests;

use Doctrine\DBAL\Exception;
use Recca0120\LaravelErdGo\ErdFinder;
use Recca0120\LaravelErdGo\Templates\ErdGo;
use Recca0120\LaravelErdGo\Tests\fixtures\Models\Car;
use Spatie\Snapshots\MatchesSnapshots;

class ErdFinderTest extends TestCase
{
    use MatchesSnapshots;

    protected function setUp(): void
    {
        parent::setUp();
        $this->template = new ErdGo();
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

    /**
     * @throws Exception
     */
    public function test_find_er_model_by_file(): void
    {
        $finder = $this->givenFinder();

        $this->assertMatchesSnapshot(
            $this->render($finder->findByFile('Car.php'))
        );
    }


    /**
     * @throws Exception
     */
    public function test_find_er_model_by_model(): void
    {
        $finder = $this->givenFinder();

        $this->assertMatchesSnapshot(
            $this->render($finder->findByModel(Car::class))
        );
    }

    /**
     * @throws Exception
     */
    public function test_find_er_model_exclude_owner(): void
    {
        $finder = $this->givenFinder();

        $this->assertMatchesSnapshot(
            $this->render($finder->findByFile('Car.php', ['owners']))
        );
    }

    private function givenFinder(): ErdFinder
    {
        return $this->app->make(ErdFinder::class)->in(__DIR__ . '/fixtures');
    }

    private function render(array $results): string
    {
        return $this->template->render($results['tables'], $results['relations']);
    }
}
