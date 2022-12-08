<?php

namespace Recca0120\LaravelErdGo\Tests\fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Owner extends Model
{
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
}