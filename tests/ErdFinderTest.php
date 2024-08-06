<?php

namespace Recca0120\LaravelErd\Tests;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\ErdFinder;
use Recca0120\LaravelErd\Factory;
use Recca0120\LaravelErd\Template\Er;
use Recca0120\LaravelErd\Template\Template;
use Recca0120\LaravelErd\Tests\fixtures\Models\Car;
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
     * @throws BindingResolutionException
     */
    public function test_find_er_model_in_directory(): void
    {
        $finder = $this->givenFinder();

        $this->assertMatchesSnapshot(
            $this->render($finder->find())
        );
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_find_er_model_by_file(): void
    {
        $finder = $this->givenFinder();

        $this->assertMatchesSnapshot(
            $this->render($finder->findByFile('Car.php'))
        );
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_find_er_model_by_model(): void
    {
        $finder = $this->givenFinder();

        $this->assertMatchesSnapshot(
            $this->render($finder->findByModel(Car::class))
        );
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_find_er_model_exclude_owner(): void
    {
        $finder = $this->givenFinder();

        $this->assertMatchesSnapshot(
            $this->render($finder->findByFile('Car.php', ['owners']))
        );
    }

    /**
     * @throws BindingResolutionException
     */
    private function givenFinder(): ErdFinder
    {
        return $this->app->make(Factory::class)->create()->in(__DIR__.'/fixtures');
    }

    private function render(Collection $results): string
    {
        return $this->template->render($results);
    }
}
