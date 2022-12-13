<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Support\ServiceProvider;
use Recca0120\LaravelErdGo\Console\Commands\ErdGoCommand;

class ErdGoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ErdFinder::class, function () {
            return (new ErdFinder(
                $this->app['db']->getDoctrineSchemaManager(), new ModelFinder(), new RelationFinder()
            ))->in(app_path());
        });

        $this->commands([ErdGoCommand::class]);
    }
}