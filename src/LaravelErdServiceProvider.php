<?php

namespace Recca0120\LaravelErd;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\ServiceProvider;
use Recca0120\LaravelErd\Console\Commands\LaravelErdCommand;

class LaravelErdServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-erd.php', 'laravel-erd');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/laravel-erd.php'], 'laravel-erd');
        }

        $this->app->singleton(AbstractSchemaManager::class, function () {
            return $this->app['db']->getDoctrineSchemaManager();
        });

        $this->app->singleton(ErdFinder::class, ErdFinder::class);

        $this->commands([LaravelErdCommand::class]);
    }
}