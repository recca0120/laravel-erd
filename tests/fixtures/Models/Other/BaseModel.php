<?php

namespace Recca0120\LaravelErd\Tests\fixtures\Models\Other;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    protected $connection = 'other';

    public function getTable(): string
    {
        return 'other_'.parent::getTable();
    }
}
