<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Relationship
{
    private string $type;
    private string $localKey;
    private string $foreignKey;

    public function __construct(string $type, string $localKey, string $foreignKey)
    {
        $this->type = $type;
        $this->localKey = $localKey;
        $this->foreignKey = $foreignKey;
    }

    public function table()
    {
        return Helpers::getTableName($this->localKey());
    }

    public function type(): string
    {
        return $this->type;
    }

    public function localKey(): string
    {
        return $this->localKey;
    }

    public function foreignKey(): string
    {
        return $this->foreignKey;
    }

    public function uniqueId(): string
    {
        $localKey = Helpers::getTableName($this->localKey());
        $foreignKey = Helpers::getTableName($this->foreignKey());

        $sortBy = [$localKey, $foreignKey];
        sort($sortBy);

        return implode('::', $sortBy);
    }

    public function sortBy(): int
    {
        if (in_array($this->type(), [BelongsTo::class, HasOne::class, MorphOne::class])) {
            return 3;
        }

        if (in_array($this->type(), [HasMany::class, MorphMany::class])) {
            return 2;
        }

        return 1;
    }

    /**
     * @param  string|string[]  $tables
     * @return bool
     */
    public function includes($tables): bool
    {
        $localTable = Helpers::getTableName($this->localKey());
        $foreignTable = Helpers::getTableName($this->foreignKey());

        return in_array($localTable, $tables, true) || in_array($foreignTable, $tables, true);
    }
}