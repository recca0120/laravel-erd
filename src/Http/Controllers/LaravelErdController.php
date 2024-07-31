<?php

namespace Recca0120\LaravelErd\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class LaravelErdController extends Controller
{
    public function index(string $file = 'laravel-erd'): View
    {
        $config = config('laravel-erd');
        $storagePath = $config['storage_path'];
        $file = ! File::extension($file) ? $file.'.'.($config['extension'] ?? 'sql') : $file;
        $extension = File::extension($file);

        $path = $storagePath.'/'.$file;
        $view = $extension === 'svg' ? 'svg' : 'vuerd';

        abort_unless(File::exists($path), 404);

        return view('laravel-erd::'.$view, ['path' => $path]);
    }
}
