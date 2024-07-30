<?php

namespace Recca0120\LaravelErd\Contracts;

interface ColumnSchema
{
    public function getName(): string;

    public function isNullable(): bool;

    public function getPrecision(): int;

    public function getType(): string;

    public function getDefault();

    public function getComment(): ?string;

    public function isAutoIncrement(): bool;
}
