<?php

namespace Recca0120\LaravelErd\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Recca0120\LaravelErd\Templates\Factory;

class LaravelErdController extends Controller
{
    public function index(Factory $factory, string $file = 'laravel-erd.ddl'): View
    {
        $factory->supports($file);
        $storagePath = config('laravel-erd.storage_path');
        $contents = base64_encode(File::get($storagePath . '/' . $file));

        return view('laravel-erd::index', compact('contents'));
    }
}