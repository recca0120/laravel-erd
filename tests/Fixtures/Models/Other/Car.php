<?php

namespace Recca0120\LaravelErd\Tests\Fixtures\Models\Other;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Car extends BaseModel
{
    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(Mechanic::class);
    }

    public function owner(): HasOne
    {
        return $this->HasOne(Owner::class);
    }
}
