<?php

namespace Recca0120\LaravelErd\Adapter\Contracts;

interface Column
{
    public function getName(): string;

    public function getNotnull(): bool;

    public function getPrecision(): int;

    public function getColumnType(): string;

    public function getDefault();

    public function getComment(): ?string;

    public function getAutoincrement(): bool;
}
