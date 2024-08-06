<?php

namespace Recca0120\LaravelErd\Contracts;

interface SchemaManagerAdapterInterface
{
    public function introspectTable(string $table): TableAdapterInterface;
}