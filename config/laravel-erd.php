<?php

return [
    'storage_path' => storage_path('framework/cache/laravel-erd'),
    'middleware' => [],
    'er' => [
        'erd-go' => env('LARAVEL_ERD_GO', '/usr/local/bin/erd-go'),
        'dot' => env('LARAVEL_ERD_DOT', '/usr/local/bin/dot'),
    ],
];
