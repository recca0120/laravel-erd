<?php

namespace Recca0120\LaravelErd\Templates;

use RuntimeException;

class Factory
{
    /** @var array<string, string> */
    private array $lookup = [
        'ddl' => DDL::class,
        'er' => Er::class,
        'svg' => Er::class,
    ];

    public function create($templateName): Template
    {
        $class = $this->lookup[$templateName] ?? Er::class;

        return new $class;
    }

    public function supports(string $file): Factory
    {
        $extension = substr($file, strrpos($file, '.') + 1);
        if (!array_key_exists($extension, $this->lookup)) {
            throw new RuntimeException('only support [' . implode(',', array_keys($this->lookup)) . ']');
        }

        return $this;
    }
}