<?php

namespace Recca0120\LaravelErd;

use Illuminate\Support\ServiceProvider;
use Recca0120\LaravelErd\Console\Commands\LaravelErdCommand;
use Recca0120\LaravelErd\Console\Commands\LaravelErdInitCommand;
use Recca0120\LaravelErd\Contracts\SchemaBuilder as SchemaBuilderContract;
use Recca0120\LaravelErd\Schema\DBAL\SchemaBuilder as DBALSchemaBuilder;
use Recca0120\LaravelErd\Schema\Laravel\SchemaBuilder as LaravelSchemaBuilder;
use Recca0120\LaravelErd\Templates\Factory;

class LaravelErdServiceProvider extends ServiceProvider
{
    public function register()
    {
        config([
            'database.connections.laravel-erd' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ]);

        $this->mergeConfigFrom(__DIR__.'/../config/laravel-erd.php', 'laravel-erd');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-erd');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/laravel-erd.php' => config_path('laravel-erd.php'),
                __DIR__.'/../resources/dist' => public_path('vendor/laravel-erd'),
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-erd'),
            ], 'laravel-erd');
        }

        $this->app->singleton(Factory::class, Factory::class);
        $this->app->singleton(ErdFinder::class, ErdFinder::class);

        $this->app->singleton(SchemaBuilderContract::class, function () {
            $connection = $this->app['db']->connection();

            return method_exists($connection, 'getDoctrineSchemaManager')
                ? new DBALSchemaBuilder($connection->getDoctrineSchemaManager())
                : new LaravelSchemaBuilder($connection->getSchemaBuilder());
        });

        $this->commands([LaravelErdInitCommand::class, LaravelErdCommand::class]);
    }
}
