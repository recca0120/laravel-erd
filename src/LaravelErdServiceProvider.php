<?php

namespace Recca0120\LaravelErd;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Client\ClientInterface;
use Recca0120\LaravelErd\Console\Commands\GenerateErd;
use Recca0120\LaravelErd\Console\Commands\InstallBinary;

class LaravelErdServiceProvider extends ServiceProvider
{
    public function register()
    {
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
        $this->app->when(InstallBinary::class)
            ->needs(ClientInterface::class)
            ->give(Client::class);

        $this->commands([InstallBinary::class, GenerateErd::class]);
    }
}
