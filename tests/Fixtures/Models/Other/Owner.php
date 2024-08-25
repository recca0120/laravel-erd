<?php

namespace Recca0120\LaravelErd\Tests\Fixtures\Models\Other;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Owner extends BaseModel
{
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}
