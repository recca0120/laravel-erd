<?php

namespace Recca0120\LaravelErd\Contracts;

use Illuminate\Support\Collection;

interface TableSchema
{
    public function getName(): string;

    /**
     * @return Collection<int, ColumnSchema>
     */
    public function getColumns(): Collection;

    /**
     * @return Collection<int, string>
     */
    public function getPrimaryKeys(): Collection;
}
