<?php

namespace Recca0120\LaravelErd\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class LaravelErdController extends Controller
{
    public function index(string $file = 'laravel-erd.sql'): View
    {
        $storagePath = config('laravel-erd.storage_path');
        $extension = substr($file, strrpos($file, '.') + 1);
        $path = $storagePath.'/'.$file;

        abort_unless(File::exists($path), 404);

        $view = $extension === 'svg' ? 'svg' : 'vuerd';

        return view('laravel-erd::'.$view, ['path' => $path]);
    }
}
