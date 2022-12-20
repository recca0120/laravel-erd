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
        $path = $storagePath . '/' . $file;
        if ($extension === 'sql') {
            $contents = base64_encode(File::get($path));

            return view('laravel-erd::vuerd', ['contents' => $contents]);
        }

        return view('laravel-erd::svg', ['path' => $path]);
    }
}