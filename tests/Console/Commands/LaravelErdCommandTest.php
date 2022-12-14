<?php

namespace Recca0120\LaravelErd\Tests\Console\Commands;

use Recca0120\LaravelErd\Tests\TestCase;

class LaravelErdCommandTest extends TestCase
{
    private string $storagePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->storagePath = realpath(__DIR__ . '/../../fixtures');
        $this->app['config']->set('laravel-erd.storage_path', $this->storagePath);
    }

    public function test_generate_svg(): void
    {
        $file = 'actual_artisan.svg';

        $this->artisan('laravel-erd', $this->givenParameters($file))->assertSuccessful();

        $contents = file_get_contents($this->storagePath . '/' . $file);
        self::assertStringContainsString('<!-- cars -->', $contents);
        self::assertStringContainsString('<!-- phones&#45;&#45;users -->', $contents);
    }

    public function test_command_not_exists(): void
    {
        $this->app['config']->set('laravel-erd.er.erd-go', '/bin/erd-go');
        $file = 'actual_artisan.svg';

        $this->artisan('laravel-erd', $this->givenParameters($file))->assertFailed();
    }

    private function givenParameters(string $file): array
    {
        return ['file' => $file, '--directory' => $this->storagePath, '--template' => 'er'];
    }
}
