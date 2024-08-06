<?php

namespace Recca0120\LaravelErd\Template;

use RuntimeException;

class Factory
{
    /** @var array<string, string> */
    private array $lookup = [
        'sql' => DDL::class,
        'er' => Er::class,
        'svg' => Er::class,
    ];

    public function create(string $file): Template
    {
        $extension = $this->getExtension($file);
        $class = $this->lookup[$extension] ?? Er::class;

        return new $class;
    }

    private function getExtension(string $file): string
    {
        $extension = substr($file, strrpos($file, '.') + 1);
        if (! array_key_exists($extension, $this->lookup)) {
            throw new RuntimeException('allow ['.implode(',', array_keys($this->lookup)).'] only');
        }

        return $extension;
    }
}
