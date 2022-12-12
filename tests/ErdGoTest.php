<?php

namespace Recca0120\LaravelErdGo\Tests;

use Doctrine\DBAL\Exception;
use Recca0120\LaravelErdGo\ErdGo;
use Recca0120\LaravelErdGo\Tests\fixtures\Models\Car;
use Spatie\Snapshots\MatchesSnapshots;

class ErdGoTest extends TestCase
{
    use MatchesSnapshots;

    /**
     * @throws Exception
     */
    public function test_generate_er_model_in_directory(): void
    {
        $erdGo = $this->givenErdGo();

        $this->assertMatchesSnapshot($erdGo->generate());
    }

    /**
     * @throws Exception
     */
    public function test_generate_er_model_by_file(): void
    {
        $erdGo = $this->givenErdGo();

        $this->assertMatchesSnapshot($erdGo->generateByFile('Car.php'));
    }

    /**
     * @throws Exception
     */
    public function test_generate_er_model_by_model(): void
    {
        $erdGo = $this->givenErdGo();

        $this->assertMatchesSnapshot($erdGo->generateByModel(Car::class));
    }

    private function givenErdGo(): ErdGo
    {
        return $this->app->make(ErdGo::class)->in(__DIR__ . '/fixtures');
    }
}
