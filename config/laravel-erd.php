<?php

return [
    'uri' => env('LARAVEL_ERD_URI', 'laravel-erd'),
    'storage_path' => storage_path('framework/cache/laravel-erd'),
    'middleware' => [],
    'binary' => [
        'erd-go' => env('LARAVEL_ERD_GO'),
        'dot' => env('LARAVEL_ERD_DOT'),
    ],
];
