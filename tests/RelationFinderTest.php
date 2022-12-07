<?php

namespace Recca0120\LaravelErdGo\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Recca0120\LaravelErdGo\RelationFinder;
use Recca0120\LaravelErdGo\Tests\fixtures\Models\Car;
use Recca0120\LaravelErdGo\Tests\fixtures\Models\Phone;
use Recca0120\LaravelErdGo\Tests\fixtures\Models\User;
use ReflectionException;

class RelationFinderTest extends TestCase
{
    use RefreshDatabase;

    private RelationFinder $finder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->finder = new RelationFinder();
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_has_one_relation(): void
    {
        $relations = $this->givenRelations(User::class);

        self::assertEquals([
            'type' => 'HasOne',
            'model' => Phone::class,
            'foreign_key' => 'phones.user_id',
            'parent_key' => 'users.id',
        ], $relations->get('phone'));
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_belongs_to_relation(): void
    {
        $relations = $this->givenRelations(Phone::class);

        self::assertEquals([
            'type' => 'BelongsTo',
            'model' => User::class,
            'foreign_key' => 'phones.user_id',
            'parent_key' => 'phones.id',
        ], $relations->get('user'));
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_belongs_to_and_has_one_relation(): void
    {
        $relations = $this->givenRelations(Car::class);

        self::assertContains('mechanic', $relations->keys());
        self::assertContains('owner', $relations->keys());
    }

    /**
     * @throws ReflectionException
     */
    private function givenRelations(string $model)
    {
        return $this->finder->generate($model);
    }
}