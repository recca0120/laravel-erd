<?php

namespace Recca0120\LaravelErdGo;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Relationship
{
    /** @var string[] */
    private static array $lookup = [
        BelongsTo::class => HasOne::class,
        MorphOne::class => HasOne::class,
        MorphMany::class => HasMany::class,
        MorphToMany::class => BelongsToMany::class,
    ];
    private string $type;
    private string $localKey;
    private string $foreignKey;

    public function __construct(string $type, string $localKey, string $foreignKey)
    {
        $this->type = $type;
        $this->localKey = $localKey;
        $this->foreignKey = $foreignKey;
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

    public function hash(): string
    {
        $relationship = Template::$relationships[$this->type];

        $sortBy = [$this->localKey, $this->foreignKey];
        sort($sortBy);

        $keys = $sortBy !== [$this->localKey, $this->foreignKey]
            ? [$this->foreignKey, $relationship, $this->localKey]
            : [$this->localKey, $relationship, $this->foreignKey];

        return md5(implode('', $keys));
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