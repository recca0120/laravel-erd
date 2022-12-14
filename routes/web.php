<?php

use Illuminate\Support\Facades\Route;
use Recca0120\LaravelErd\Http\Controllers\LaravelErdController;

Route::get('laravel-erd/{file?}', [LaravelErdController::class, 'index'])
    ->name('laravel-erd.index');