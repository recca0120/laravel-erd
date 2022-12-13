<?php

namespace Recca0120\LaravelErdGo\Tests\Console\Commands;

use Recca0120\LaravelErdGo\Tests\TestCase;

class ErdGoCommandTest extends TestCase
{
    public function test_generate_svg(): void
    {
        $file = __DIR__ . '/../../fixtures/actual_artisan.svg';

        $parameters = ['file' => $file, '--directory' => __DIR__ . '/../../fixtures'];
        $this->artisan('erd-go', $parameters)->assertSuccessful();

        self::assertFileEquals(__DIR__ . '/../../fixtures/expected_artisan.svg', $file);
    }

    public function test_command_not_exists(): void
    {
        $this->app['config']->set('erd-go.erd-go', '/bin/erd-go');
        $file = __DIR__ . '/../../fixtures/actual_artisan.svg';

        $parameters = ['file' => $file, '--directory' => __DIR__ . '/../../fixtures'];
        $this->artisan('erd-go', $parameters)->assertFailed();
    }
}
