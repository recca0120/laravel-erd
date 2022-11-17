<?php

namespace Recca0120\LaravelErdGo\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Recca0120\LaravelErdGo\ErdGo;

class ErdGoTest extends TestCase
{
    use RefreshDatabase;

    public function test_erd_go(): void
    {
        $schemaManager = DB::connection()->getDoctrineSchemaManager();
        $erd = new ErdGo($schemaManager);
        $erd->generate(['users', 'phones']);
    }
}