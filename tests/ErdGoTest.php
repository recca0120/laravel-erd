<?php

namespace Recca0120\LaravelErdGo\Tests;

use Recca0120\LaravelErdGo\ErdGo;
use Recca0120\LaravelErdGo\ModelFinder;
use Recca0120\LaravelErdGo\RelationFinder;

class ErdGoTest extends TestCase
{
    public function test_generate(): void
    {
        $erdGo = new ErdGo(new ModelFinder(), new RelationFinder());

        $erdGo->generate(__DIR__ . '/fixtures');
    }
}
