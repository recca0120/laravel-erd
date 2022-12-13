<?php

namespace Recca0120\LaravelErdGo;

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

        $this->app->singleton(ErdFinder::class, function () {
            return (new ErdFinder(
                $this->app['db']->getDoctrineSchemaManager(), new ModelFinder(), new RelationFinder()
            ));
        });

        $this->commands([ErdGoCommand::class]);
    }
}