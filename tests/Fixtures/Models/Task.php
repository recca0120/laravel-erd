<?php

namespace Recca0120\LaravelErd\Tests\Fixtures\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use Compoships;

    public function user()
    {
        return $this->belongsTo(User::class, ['team_id', 'category_id'], ['team_id', 'category_id']);
    }
}