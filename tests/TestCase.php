<?php

namespace Recca0120\LaravelErd\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Recca0120\LaravelErd\LaravelErdServiceProvider;
use Spatie\Permission\PermissionServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            PermissionServiceProvider::class,
            LaravelErdServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('laravel-erd.er', [
            'erd-go' => __DIR__ . '/fixtures/bin/erd-go',
            'dot' => __DIR__ . '/fixtures/bin/dot',
        ]);
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
//            'driver' => 'mysql',
//            'database' => 'test',
//            'host' => '127.0.0.1',
//            'username' => 'root',
//            'prefix' => '',
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function refreshApplication(): void
    {
        parent::refreshApplication();
        $this->artisan('laravel-erd:init');
    }
}