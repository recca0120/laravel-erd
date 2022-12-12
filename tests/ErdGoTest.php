<?php

namespace Recca0120\LaravelErdGo\Tests;

use Recca0120\LaravelErdGo\ErdGo;
use Spatie\Snapshots\MatchesSnapshots;

class ErdGoTest extends TestCase
{
    use MatchesSnapshots;

    public function test_generate(): void
    {
        /** @var ErdGo $erdGo */
        $erdGo = $this->app->make(ErdGo::class);

        $this->assertMatchesSnapshot($erdGo->generate(__DIR__ . '/fixtures'));
    }
}
