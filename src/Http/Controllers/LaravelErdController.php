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
        $contents = base64_encode(File::get($storagePath . '/' . $file));

        return view('laravel-erd::index', compact('contents'));
    }
}