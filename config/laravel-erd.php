<?php

return [
    'uri' => env('LARAVEL_ERD_URI', 'laravel-erd'),
    'storage_path' => storage_path('framework/cache/laravel-erd'),
    'middleware' => [],
    'binary' => [
        'erd-go' => env('LARAVEL_ERD_GO', '/usr/local/bin/erd-go'),
        'dot' => env('LARAVEL_ERD_DOT', '/usr/local/bin/dot'),
    ],
];
