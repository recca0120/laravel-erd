<?php

namespace Recca0120\LaravelErd\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class LaravelErdController extends Controller
{
    public function index(string $file = 'laravel-erd.sql'): View
    {
        $storagePath = config('laravel-erd.storage_path');
        $extension = substr($file, strrpos($file, '.') + 1);
        $path = $storagePath . '/' . $file;
        $view = $extension === 'svg' ? 'svg' : 'vuerd';

        return view('laravel-erd::' . $view, ['path' => $path]);
    }
}