<?php

namespace Recca0120\LaravelErd\Tests\Console\Commands;

use Recca0120\LaravelErd\Tests\TestCase;

class LaravelErdCommandTest extends TestCase
{
    public function test_generate_svg(): void
    {
        $file = __DIR__ . '/../../fixtures/actual_artisan.svg';

        $this->artisan('laravel-erd', $this->givenParameters($file))->assertSuccessful();

        self::assertFileEquals(__DIR__ . '/../../fixtures/expected_artisan.svg', $file);
    }

    public function test_command_not_exists(): void
    {
        $this->app['config']->set('laravel-erd.er.erd-go', '/bin/erd-go');
        $file = __DIR__ . '/../../fixtures/actual_artisan.svg';

        $this->artisan('laravel-erd', $this->givenParameters($file))->assertFailed();
    }

    private function givenParameters(string $file): array
    {
        return [
            'file' => $file,
            '--directory' => __DIR__ . '/../../fixtures',
            '--template' => 'er',
        ];
    }
}
