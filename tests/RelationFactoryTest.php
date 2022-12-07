<?php

namespace Recca0120\LaravelErdGo\Tests;

use Illuminate\Container\Container;
use Recca0120\LaravelErdGo\RelationFinder;
use Recca0120\LaravelErdGo\Tests\fixtures\Models\Phone;
use Recca0120\LaravelErdGo\Tests\fixtures\Models\User;
use ReflectionException;

class RelationFactoryTest extends TestCase
{
    private RelationFinder $finder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->finder = new RelationFinder(new Container());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_has_one_relation(): void
    {
        $relations = $this->givenRelations(User::class);

        self::assertEquals([
            "phone",
            "HasOne",
            Phone::class,
            "id",
            "user_id",
        ], $relations->get('phone'));
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_belongs_to_relation(): void
    {
        $relations = $this->givenRelations(Phone::class);

        self::assertEquals([
            "user",
            "BelongsTo",
            User::class,
            "user_id",
            "id",
        ], $relations->get('user'));
    }

    /**
     * @throws ReflectionException
     */
    private function givenRelations(string $model)
    {
        return $this->finder->generate($model);
    }
}