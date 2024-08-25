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
        $app['config']->set('laravel-erd.binary', [
            'erd-go' => __DIR__.'/Fixtures/bin/erd-go',
            'dot' => __DIR__.'/Fixtures/bin/dot',
        ]);
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('database.connections.other', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        foreach ([5 => 'add_teams_fields', 6 => 'create_permission_tables'] as $index => $migrationFile) {
            copy(
                __DIR__.'/../vendor/spatie/laravel-permission/database/migrations/'.$migrationFile.'.php.stub',
                __DIR__.'/../database/migrations/2022_12_07_00000'.$index.'_'.$migrationFile.'.php'
            );
        }

        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    protected function refreshApplication(): void
    {
        parent::refreshApplication();
        $this->artisan('erd:install');
    }
}
