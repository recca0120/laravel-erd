<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Support\ServiceProvider;
use Recca0120\LaravelErdGo\Console\Commands\ErdGoCommand;

class ErdGoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/erd-go.php', 'erd-go');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/erd-go.php'], 'erd-go');
        }

        $this->app->singleton(AbstractSchemaManager::class, function () {
            return $this->app['db']->getDoctrineSchemaManager();
        });

        $this->app->singleton(ErdFinder::class, ErdFinder::class);

        $this->commands([ErdGoCommand::class]);
    }
}