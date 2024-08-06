<?php

namespace Recca0120\LaravelErd\Contracts;

use Illuminate\Support\Collection;

interface TableAdapterInterface
{
    public function getName(): string;

    /**
     * @return Collection<int, ColumnAdapterInterface>
     */
    public function getColumns(): Collection;

    public function getPrimaryKeys(): Collection;
}