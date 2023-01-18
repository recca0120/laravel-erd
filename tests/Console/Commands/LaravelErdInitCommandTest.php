<?php

namespace Recca0120\LaravelErd\Tests\Console\Commands;

use Recca0120\LaravelErd\Tests\TestCase;

class LaravelErdInitCommandTest extends TestCase
{
    public function test_download_binary(): void
    {
        $this->artisan('laravel-erd:init')->assertSuccessful();
    }
}
