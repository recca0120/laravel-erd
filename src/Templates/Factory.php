<?php

namespace Recca0120\LaravelErd\Templates;

class Factory
{
    /** @var array<string, string> */
    private array $lookup;

    public function __construct()
    {
        $this->lookup = [
            'ddl' => DDL::class,
            DDL::class => DDL::class,
            'er' => Er::class,
            Er::class => Er::class,
        ];
    }

    public function create($templateName): Template
    {
        $class = $this->lookup[$templateName] ?? Er::class;

        return new $class;
    }
}