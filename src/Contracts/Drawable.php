<?php

namespace Recca0120\LaravelErdGo\Contracts;

interface Drawable
{
    public function localKey(): string;

    public function foreignKey(): string;
}