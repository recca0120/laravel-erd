<?php

namespace Recca0120\LaravelErdGo;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\TypeRegistry;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Template
{
    /** @var string[] */
    public static array $relationships = [
        BelongsTo::class => '1--1',
        HasOne::class => '1--1',
        MorphOne::class => '1--1',
        HasMany::class => '1--*',
        MorphMany::class => '1--*',
        BelongsToMany::class => '*--*',
        MorphToMany::class => '*--*'
    ];

    public function renderTable(Table $table): string
    {
        $result = sprintf("[%s] {}\n", $table->name());
        $result .= collect($table->columns())
                ->map(fn(Column $column) => $this->renderColumn($column))
                ->implode("\n") . "\n";

        return $result;
    }

    public function renderRelationship(Relationship $relationship): string
    {
        return sprintf(
            '%s %s %s',
            $this->getTableName($relationship->localKey()),
            self::$relationships[$relationship->type()],
            $this->getTableName($relationship->foreignKey())
        );
    }

    private function renderColumn(Column $column): string
    {
        return sprintf(
            '%s {label: "%s, %s"}',
            $column->getName(),
            $this->getColumnType($column),
            $column->getNotnull() ? 'not null' : 'null'
        );
    }

    protected function getColumnType(Column $column): string
    {
        try {
            return $this->getTypeRegistry()->lookupName($column->getType());
        } catch (Exception $e) {
            return 'unknown';
        }
    }

    protected function getTableName(string $qualifiedKeyName)
    {
        return substr($qualifiedKeyName, 0, strpos($qualifiedKeyName, '.'));
    }

    private function getTypeRegistry(): TypeRegistry
    {
        return Type::getTypeRegistry();
    }
}