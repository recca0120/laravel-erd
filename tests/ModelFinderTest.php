<?php

namespace Recca0120\LaravelErd\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\ModelFinder;
use Recca0120\LaravelErd\Tests\Fixtures\Models\BaseModel;
use Recca0120\LaravelErd\Tests\Fixtures\Models\Phone;
use Recca0120\LaravelErd\Tests\Fixtures\Models\User;
use Recca0120\LaravelErd\Tests\Fixtures\NonModel;

class ModelFinderTest extends TestCase
{
    use RefreshDatabase;

    private Collection $files;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = (new ModelFinder)->find(__DIR__.'/Fixtures', '*.php');
    }

    public function test_it_should_find_user_model(): void
    {
        self::assertContains(User::class, $this->files);
    }

    public function test_it_should_find_phone_model(): void
    {
        self::assertContains(Phone::class, $this->files);
    }

    public function test_it_should_not_find_base_model(): void
    {
        self::assertNotContains(BaseModel::class, $this->files);
    }

    public function test_it_should_not_find_not_eloquent_model(): void
    {
        self::assertNotContains(NonModel::class, $this->files);
    }
}
