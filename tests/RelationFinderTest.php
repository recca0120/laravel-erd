<?php

namespace Recca0120\LaravelErdGo\Tests;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Recca0120\LaravelErdGo\Relation;
use Recca0120\LaravelErdGo\RelationFinder;
use Recca0120\LaravelErdGo\Tests\fixtures\Models\Car;
use Recca0120\LaravelErdGo\Tests\fixtures\Models\Comment;
use Recca0120\LaravelErdGo\Tests\fixtures\Models\Mechanic;
use Recca0120\LaravelErdGo\Tests\fixtures\Models\Owner;
use Recca0120\LaravelErdGo\Tests\fixtures\Models\Post;
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
    public function test_find_mechanic_relations(): void
    {
        $relations = $this->givenRelations(Mechanic::class);

        /** @var Relation $car */
        $car = $relations->get('car');
        self::assertEquals(HasOne::class, $car->type());
        self::assertEquals(Car::class, $car->related());
        self::assertEquals('id', $car->localKey());
        self::assertEquals('mechanic_id', $car->foreignKey());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_car_relations(): void
    {
        $relations = $this->givenRelations(Car::class);

        /** @var Relation $mechanic */
        $mechanic = $relations->get('mechanic');
        self::assertEquals(BelongsTo::class, $mechanic->type());
        self::assertEquals(Mechanic::class, $mechanic->related());
        self::assertEquals('mechanic_id', $mechanic->localKey());
        self::assertEquals('id', $mechanic->foreignKey());

        /** @var Relation $owner */
        $owner = $relations->get('owner');
        self::assertEquals(HasOne::class, $owner->type());
        self::assertEquals(Owner::class, $owner->related());
        self::assertEquals('id', $owner->localKey());
        self::assertEquals('car_id', $owner->foreignKey());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_owner_relations(): void
    {
        $relations = $this->givenRelations(Owner::class);

        /** @var Relation $car */
        $car = $relations->get('car');
        self::assertEquals(BelongsTo::class, $car->type());
        self::assertEquals(Car::class, $car->related());
        self::assertEquals('car_id', $car->localKey());
        self::assertEquals('id', $car->foreignKey());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_post_relations(): void
    {
        $relations = $this->givenRelations(Post::class);

        /** @var Relation $comments */
        $comments = $relations->get('comments');
        self::assertEquals(HasMany::class, $comments->type());
        self::assertEquals(Comment::class, $comments->related());
        self::assertEquals('id', $comments->localKey());
        self::assertEquals('post_id', $comments->foreignKey());
    }

    /**
     * @throws ReflectionException
     */
    private function givenRelations(string $model): Collection
    {
        return $this->finder->generate($model);
    }
}