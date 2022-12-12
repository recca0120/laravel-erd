<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Support\ServiceProvider;

class ErdGoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ErdGo::class, function () {
            return new ErdGo(
                $this->app['db']->getDoctrineSchemaManager(), new ModelFinder(), new RelationFinder()
            );
        });
    }
}