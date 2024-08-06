<?php

namespace Recca0120\LaravelErd\Contracts;

interface ColumnAdapterInterface
{
    public function getPrecision(): int;

    public function getDefault();

    public function getComment(): ?string;

    public function getName(): string;

    public function getNotnull(): bool;

    public function getAutoincrement(): bool;

    public function getType(): string;
}