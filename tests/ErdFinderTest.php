<?php

namespace Recca0120\LaravelErd\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\ErdFinder;
use Recca0120\LaravelErd\Factory;
use Recca0120\LaravelErd\Template\Er;
use Recca0120\LaravelErd\Template\Template;
use Recca0120\LaravelErd\Tests\Fixtures\Models\Car;
use ReflectionException;
use Spatie\Snapshots\MatchesSnapshots;

class ErdFinderTest extends TestCase
{
    use MatchesSnapshots;
    use RefreshDatabase;

    private Template $template;

    protected function setUp(): void
    {
        parent::setUp();
        $this->template = new Er();
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_er_model_in_directory(): void
    {
        $finder = $this->givenFinder();

        $this->assertMatchesSnapshot(
            $this->render($finder->find('*.php', ['user_device', 'devices']))
        );
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_er_model_by_file(): void
    {
        $finder = $this->givenFinder();

        $this->assertMatchesSnapshot(
            $this->render($finder->findByFile('Car.php'))
        );
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_er_model_by_model(): void
    {
        $finder = $this->givenFinder();

        $this->assertMatchesSnapshot(
            $this->render($finder->findByModel(Car::class))
        );
    }

    /**
     * @throws ReflectionException
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
        return $this->app->make(Factory::class)->create()->in(__DIR__.'/Fixtures');
    }

    private function render(Collection $results): string
    {
        return $this->template->render($results);
    }
}
