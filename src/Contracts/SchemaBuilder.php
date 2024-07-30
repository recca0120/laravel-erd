<?php

namespace Recca0120\LaravelErd\Contracts;

interface SchemaBuilder
{
    public function getTableSchema(string $name): TableSchema;
}
