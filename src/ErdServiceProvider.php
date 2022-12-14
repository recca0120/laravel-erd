<?php

namespace Recca0120\LaravelErd;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\ServiceProvider;
use Recca0120\LaravelErd\Console\Commands\ErdCommand;

class ErdServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/erd.php', 'erd');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/erd.php'], 'erd');
        }

        $this->app->singleton(AbstractSchemaManager::class, function () {
            return $this->app['db']->getDoctrineSchemaManager();
        });

        $this->app->singleton(ErdFinder::class, ErdFinder::class);

        $this->commands([ErdCommand::class]);
    }
}