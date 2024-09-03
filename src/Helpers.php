<?php

namespace Recca0120\LaravelErd;

class Helpers
{
    public static function getTableName(string|array|null $qualifiedKeyName): ?string
    {
        if (is_array($qualifiedKeyName)) {
            return implode('-', $qualifiedKeyName);
        }

        return substr($qualifiedKeyName, 0, strpos($qualifiedKeyName, '.'));
    }

    public static function getColumnName(string|array|null $qualifiedKeyName): ?string
    {
        if (is_array($qualifiedKeyName)) {
            return implode('-', $qualifiedKeyName);
        }

        return $qualifiedKeyName && str_contains($qualifiedKeyName, '.')
            ? substr($qualifiedKeyName, strpos($qualifiedKeyName, '.') + 1)
            : $qualifiedKeyName;
    }
}
