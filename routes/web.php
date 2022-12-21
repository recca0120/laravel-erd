<?php

use Illuminate\Support\Facades\Route;
use Recca0120\LaravelErd\Http\Controllers\LaravelErdController;

Route::get(config('laravel-erd.uri') . '/{file?}', [LaravelErdController::class, 'index'])
    ->name('laravel-erd.show')
    ->middleware(config('laravel-erd.middleware'))
    ->where('file', '.*');