<?php

namespace Recca0120\LaravelErd\Adapter\Contracts;

use Illuminate\Support\Collection;

interface Table
{
    public function getName(): string;

    /**
     * @return Collection<int, Column>
     */
    public function getColumns(): Collection;

    /**
     * @return Collection<int, string>
     */
    public function getPrimaryKey(): Collection;
}
