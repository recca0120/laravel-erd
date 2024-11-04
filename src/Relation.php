<?php

namespace Recca0120\LaravelErd;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Relation
{
    private static array $relationMap = [
        \Awobaz\Compoships\Database\Eloquent\Relations\BelongsTo::class => BelongsTo::class,
        \Awobaz\Compoships\Database\Eloquent\Relations\HasOne::class => HasOne::class,
        \Awobaz\Compoships\Database\Eloquent\Relations\HasMany::class => HasMany::class,
    ];
    private array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function type(): string
    {
        return self::$relationMap[$this->attributes['type']] ?? $this->attributes['type'];
    }

    public function related(): string
    {
        return $this->attributes['related'];
    }

    public function parent(): string
    {
        return $this->attributes['parent'];
    }

    public function localKeys(): array
    {
        return (array) $this->attributes['local_key'];
    }

    public function localTable(): string
    {
        return Helpers::getTableName($this->localKeys()[0]);
    }

    public function localColumns(): array
    {
        return array_map(static function (string $column) {
            return Helpers::getColumnName($column);
        }, $this->localKeys());
    }

    public function foreignKeys(): array
    {
        return (array) $this->attributes['foreign_key'];
    }

    public function foreignTable(): string
    {
        return Helpers::getTableName($this->foreignKeys()[0]);
    }

    public function foreignColumns(): array
    {
        return array_map(static function (string $column) {
            return Helpers::getColumnName($column);
        }, $this->foreignKeys());
    }

    public function morphClass(): ?string
    {
        return $this->attributes['morph_class'] ?? null;
    }

    public function morphType(): ?string
    {
        return $this->attributes['morph_type'] ?? null;
    }

    public function morphColumn(): ?string
    {
        return Helpers::getColumnName($this->morphType());
    }

    public function connection(): ?string
    {
        $model = $this->related();

        return (new $model())->getConnectionName();
    }

    public function pivot(): ?Pivot
    {
        return array_key_exists('pivot', $this->attributes)
            ? new Pivot($this->attributes['pivot'])
            : null;
    }

    /**
     * @param  string[]  $tables
     */
    public function excludes(array $tables): bool
    {
        $localTable = $this->localTable();
        $foreignTable = $this->foreignTable();

        return in_array($localTable, $tables, true) || in_array($foreignTable, $tables, true);
    }

    public function relatedRelation(): Relation
    {
        $reverseLookup = [
            BelongsTo::class => HasMany::class,
            HasOne::class => BelongsTo::class,
            MorphOne::class => MorphTo::class,
            HasMany::class => BelongsTo::class,
            MorphMany::class => MorphTo::class,
        ];

        $type = $this->type();

        return new Relation(array_filter([
            'type' => $reverseLookup[$type] ?? $type,
            'related' => $this->parent(),
            'parent' => $this->related(),
            'local_key' => $this->foreignKeys(),
            'foreign_key' => $this->localKeys(),
            'pivot' => $this->attributes['pivot'] ?? null,
            'morph_class' => $this->morphClass(),
            'morph_type' => $this->morphType(),
        ]));
    }

    public function sortByRelation(): int
    {
        $relationGroups = [
            [BelongsTo::class, HasOne::class, MorphOne::class],
            [HasMany::class, MorphMany::class],
        ];

        $type = $this->type();
        foreach ($relationGroups as $index => $relations) {
            if (in_array($type, $relations, true)) {
                return $index + 1;
            }
        }

        return count($relationGroups);
    }

    /**
     * @return string[]
     */
    public function sortByKeys(): array
    {
        return [$this->type(), $this->localKeys(), $this->foreignKeys()];
    }

    public function uniqueId(): string
    {
        $sortBy = [];
        foreach ($this->localKeys() as $localKey) {
            $sortBy[] = Helpers::getTableName($localKey);
        }
        foreach ($this->foreignKeys() as $foreignKey) {
            $sortBy[] = Helpers::getTableName($foreignKey);
        }

        sort($sortBy);

        return implode('::', $sortBy);
    }
}
