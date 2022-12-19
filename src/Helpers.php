<?php

namespace Recca0120\LaravelErd;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;

class Helpers
{
    public static function getTableName(string $qualifiedKeyName): string
    {
        return substr($qualifiedKeyName, 0, strpos($qualifiedKeyName, '.'));
    }

    public static function getColumnName(string $qualifiedKeyName): string
    {
        return strpos($qualifiedKeyName, '.') !== false
            ? substr($qualifiedKeyName, strpos($qualifiedKeyName, '.') + 1)
            : $qualifiedKeyName;
    }

    public static function getColumnType(Column $column): string
    {
        try {
            return Type::getTypeRegistry()->lookupName($column->getType());
        } catch (Exception $e) {
            return 'unknown';
        }
    }

}