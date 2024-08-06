<?php

namespace Recca0120\LaravelErd;

class Helpers
{
    public static function getTableName(string $qualifiedKeyName): string
    {
        return substr($qualifiedKeyName, 0, strpos($qualifiedKeyName, '.'));
    }

    public static function getColumnName(?string $qualifiedKeyName): ?string
    {
        return $qualifiedKeyName && str_contains($qualifiedKeyName, '.')
            ? substr($qualifiedKeyName, strpos($qualifiedKeyName, '.') + 1)
            : $qualifiedKeyName;
    }
}
