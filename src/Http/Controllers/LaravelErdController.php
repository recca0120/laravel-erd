<?php

namespace Recca0120\LaravelErd\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class LaravelErdController extends Controller
{
    public function index($file = 'laravel-erd.ddl')
    {
        $storagePath = config('laravel-erd.storage_path');
        $contents = base64_encode(File::get($storagePath . '/' . $file));

        return view('laravel-erd::index', compact('contents'));
    }
}