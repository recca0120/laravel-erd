<?php

namespace Recca0120\LaravelErd\Adapter\Contracts;

interface SchemaManager
{
    public function introspectTable(string $name): Table;
}
