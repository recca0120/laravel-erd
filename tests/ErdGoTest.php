<?php

namespace Recca0120\LaravelErdGo\Tests;

use Illuminate\Support\Facades\DB;
use Recca0120\LaravelErdGo\ErdGo;
use Recca0120\LaravelErdGo\ModelFinder;
use Recca0120\LaravelErdGo\RelationFinder;
use Spatie\Snapshots\MatchesSnapshots;

class ErdGoTest extends TestCase
{
    use MatchesSnapshots;

    public function test_generate(): void
    {
        $schemaManager = DB::connection()->getDoctrineSchemaManager();
        $erdGo = new ErdGo($schemaManager, new ModelFinder(), new RelationFinder());

        $this->assertMatchesSnapshot($erdGo->generate(__DIR__ . '/fixtures'));
    }
}
