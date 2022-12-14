<?php

namespace Recca0120\LaravelErd\Tests\Console\Commands;

use Recca0120\LaravelErd\Tests\TestCase;

class ErdCommandTest extends TestCase
{
    public function test_generate_svg(): void
    {
        $file = __DIR__ . '/../../fixtures/actual_artisan.svg';

        $parameters = ['file' => $file, '--directory' => __DIR__ . '/../../fixtures'];
        $this->artisan('erd', $parameters)->assertSuccessful();

        self::assertFileEquals(__DIR__ . '/../../fixtures/expected_artisan.svg', $file);
    }

    public function test_command_not_exists(): void
    {
        $this->app['config']->set('erd.er.erd-go', '/bin/erd-go');
        $file = __DIR__ . '/../../fixtures/actual_artisan.svg';

        $parameters = ['file' => $file, '--directory' => __DIR__ . '/../../fixtures'];
        $this->artisan('erd', $parameters)->assertFailed();
    }
}
