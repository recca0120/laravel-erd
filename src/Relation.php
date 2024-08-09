<?php

namespace Recca0120\LaravelErd;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Relation
{
    private array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function type(): string
    {
        return $this->attributes['type'];
    }

    public function related(): string
    {
        return $this->attributes['related'];
    }

    public function parent(): string
    {
        return $this->attributes['parent'];
    }

    public function table(): string
    {
        return Helpers::getTableName($this->localKey());
    }

    public function localKey(): string
    {
        return $this->attributes['local_key'];
    }

    public function localTable(): string
    {
        return Helpers::getTableName($this->localKey());
    }

    public function localColumn(): string
    {
        return Helpers::getColumnName($this->localKey());
    }

    public function foreignKey(): string
    {
        return $this->attributes['foreign_key'];
    }

    public function foreignTable(): string
    {
        return Helpers::getTableName($this->foreignKey());
    }

    public function foreignColumn(): string
    {
        return Helpers::getColumnName($this->foreignKey());
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
        $model = $this->type() === BelongsTo::class ? $this->parent() : $this->related();

        return (new $model)->getConnectionName();
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
        return new Relation([
            'type' => $this->type(),
            'local_key' => $this->foreignKey(),
            'foreign_key' => $this->localKey(),
            'related' => $this->attributes['parent'],
            'parent' => $this->attributes['related'],
        ]);
    }

    public function order(): int
    {
        if (in_array($this->type(), [BelongsTo::class, HasOne::class, MorphOne::class])) {
            return 1;
        }

        if (in_array($this->type(), [HasMany::class, MorphMany::class])) {
            return 2;
        }

        return 3;
    }

    public function uniqueId(): string
    {
        $localKey = Helpers::getTableName($this->localKey());
        $foreignKey = Helpers::getTableName($this->foreignKey());

        $sortBy = [$localKey, $foreignKey];
        sort($sortBy);

        return implode('::', $sortBy);
    }

    /**
     * @return string[]
     */
    public function unique(): array
    {
        return [$this->type(), $this->localKey(), $this->foreignKey()];
    }
}
