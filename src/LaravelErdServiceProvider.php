<?php

namespace Recca0120\LaravelErd;

use Illuminate\Support\ServiceProvider;
use Recca0120\LaravelErd\Console\Commands\DownloadBinary;
use Recca0120\LaravelErd\Console\Commands\GenerateErd;

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

        $this->commands([DownloadBinary::class, GenerateErd::class]);
    }
}
