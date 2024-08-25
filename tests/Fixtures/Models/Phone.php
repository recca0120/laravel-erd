<?php

namespace Recca0120\LaravelErd\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Phone extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
