<?php

namespace Recca0120\LaravelErd\Tests\fixtures\Models\Other;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Recca0120\LaravelErd\Tests\fixtures\Models\User;

class Phone extends BaseModel
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
