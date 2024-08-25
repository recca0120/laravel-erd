<?php

namespace Recca0120\LaravelErd\Tests\Fixtures\Models\Other;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Mechanic extends BaseModel
{
    public function car(): HasOne
    {
        return $this->hasOne(Car::class);
    }

    public function carOwner(): HasOneThrough
    {
        return $this->hasOneThrough(Owner::class, Car::class);
    }
}
