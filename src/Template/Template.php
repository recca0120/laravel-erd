<?php

namespace Recca0120\LaravelErd\Template;

use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Table;

interface Template
{
    /**
     * @param  Collection<int|string, Table>  $tables
     */
    public function render(Collection $tables): string;

    /**
     * @param  array<string, string>  $options
     */
    public function save(string $output, string $path, array $options = []): int;
}
