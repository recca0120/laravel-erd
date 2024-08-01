<?php

namespace Recca0120\LaravelErd\Tests\Console\Commands;

use Illuminate\Support\Facades\File;
use Recca0120\LaravelErd\Tests\TestCase;

class LaravelErdCommandTest extends TestCase
{
    private string $storagePath;

    private string $file = 'actual_artisan.svg';

    protected function setUp(): void
    {
        parent::setUp();
        $this->storagePath = realpath(__DIR__.'/../../fixtures');
        $this->app['config']->set('laravel-erd.binary', [
            'erd-go' => $this->storagePath.'/bin/erd-go',
            'dot' => $this->storagePath.'/bin/dot',
        ]);
        $this->app['config']->set('laravel-erd.storage_path', $this->storagePath);
        $this->app['config']->set([
            'passport.storage.database.connection' => 'testbench',
            'telescope.storage.database.connection' => 'testbench',
        ]);
    }

    protected function tearDown(): void
    {
        File::delete($this->storagePath.'/'.$this->file);
        self::assertEquals([
            'database.default' => 'laravel-erd',
            'passport.storage.database.connection' => 'laravel-erd',
            'telescope.storage.database.connection' => 'laravel-erd',
        ], [
            'database.default' => config('database.default'),
            'passport.storage.database.connection' => config('passport.storage.database.connection'),
            'telescope.storage.database.connection' => config('telescope.storage.database.connection'),
        ]);

        parent::tearDown();
    }

    public function test_generate_svg(): void
    {
        $this->artisan('erd:generate', $this->givenParameters($this->file))->assertSuccessful();

        $contents = file_get_contents($this->storagePath.'/'.$this->file);
        self::assertStringContainsString('<!-- cars -->', $contents);
        self::assertStringContainsString('<!-- phones&#45;&#45;users -->', $contents);
    }

    public function test_command_not_exists(): void
    {
        $this->app['config']->set('laravel-erd.binary.erd-go', '/bin/erd-go');

        $this->artisan('erd:generate', $this->givenParameters($this->file))->assertFailed();
    }

    private function givenParameters(string $file): array
    {
        return ['file' => $file, '--directory' => $this->storagePath];
    }
}
