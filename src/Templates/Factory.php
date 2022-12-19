<?php

namespace Recca0120\LaravelErd\Templates;

use RuntimeException;

class Factory
{
    /** @var array<string, string> */
    private array $lookup = [
        'sql' => DDL::class,
        'er' => Er::class,
        'svg' => Er::class,
    ];

    public function create(string $templateName): Template
    {
        $class = $this->lookup[$templateName] ?? Er::class;

        return new $class;
    }

    public function allowFileExtension(string $file): Factory
    {
        $extension = substr($file, strrpos($file, '.') + 1);
        if (!array_key_exists($extension, $this->lookup)) {
            throw new RuntimeException('allow [' . implode(',', array_keys($this->lookup)) . '] only');
        }

        return $this;
    }
}