<?php

namespace Recca0120\LaravelErd\Templates;

use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Table;

interface Template
{
    /**
     * @param  Collection<int|string, Table>  $tables
     * @return string
     */
    public function render(Collection $tables): string;

    /**
     * @param  string  $output
     * @param  string  $path
     * @param  array<string, string>  $options
     * @return int
     */
    public function save(string $output, string $path, array $options = []): int;
}