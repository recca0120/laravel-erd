<?php

namespace Recca0120\LaravelErd\Tests;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Helpers;
use Recca0120\LaravelErd\Relation;
use Recca0120\LaravelErd\RelationFinder;
use Recca0120\LaravelErd\Tests\fixtures\Models\Car;
use Recca0120\LaravelErd\Tests\fixtures\Models\Comment;
use Recca0120\LaravelErd\Tests\fixtures\Models\Image;
use Recca0120\LaravelErd\Tests\fixtures\Models\Mechanic;
use Recca0120\LaravelErd\Tests\fixtures\Models\Owner;
use Recca0120\LaravelErd\Tests\fixtures\Models\Post;
use Recca0120\LaravelErd\Tests\fixtures\Models\User;
use ReflectionException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RelationFinderTest extends TestCase
{
    use RefreshDatabase;

    private static array $relationships = [
        BelongsTo::class => '1--1',
        HasOne::class => '1--1',
        MorphOne::class => '1--1',
        HasMany::class => '1--*',
        MorphMany::class => '1--*',
        BelongsToMany::class => '*--*',
        MorphToMany::class => '*--*',
    ];

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
        $car = $relations->get('car')->firstOrFail();
        self::assertEquals(HasOne::class, $car->type());
        self::assertEquals(Car::class, $car->related());
        self::assertEquals('mechanics.id', $car->localKey());
        self::assertEquals('cars.mechanic_id', $car->foreignKey());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_car_relations(): void
    {
        $relations = $this->givenRelations(Car::class);

        /** @var Relation $mechanic */
        $mechanic = $relations->get('mechanic')->firstOrFail();
        self::assertEquals(BelongsTo::class, $mechanic->type());
        self::assertEquals(Mechanic::class, $mechanic->related());
        self::assertEquals('cars.mechanic_id', $mechanic->localKey());
        self::assertEquals('mechanics.id', $mechanic->foreignKey());

        /** @var Relation $owner */
        $owner = $relations->get('owner')->firstOrFail();
        self::assertEquals(HasOne::class, $owner->type());
        self::assertEquals(Owner::class, $owner->related());
        self::assertEquals('cars.id', $owner->localKey());
        self::assertEquals('owners.car_id', $owner->foreignKey());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_owner_relations(): void
    {
        $relations = $this->givenRelations(Owner::class);

        /** @var Relation $car */
        $car = $relations->get('car')->firstOrFail();
        self::assertEquals(BelongsTo::class, $car->type());
        self::assertEquals(Car::class, $car->related());
        self::assertEquals('owners.car_id', $car->localKey());
        self::assertEquals('cars.id', $car->foreignKey());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_post_relations(): void
    {
        $relations = $this->givenRelations(Post::class);

        /** @var Relation $comments */
        $comments = $relations->get('comments')->firstOrFail();
        self::assertEquals(HasMany::class, $comments->type());
        self::assertEquals(Comment::class, $comments->related());
        self::assertEquals('posts.id', $comments->localKey());
        self::assertEquals('comments.post_id', $comments->foreignKey());

        /** @var Relation $user */
        $user = $relations->get('user')->firstOrFail();
        self::assertEquals(BelongsTo::class, $user->type());
        self::assertEquals(User::class, $user->related());
        self::assertEquals('posts.user_id', $user->localKey());
        self::assertEquals('users.id', $user->foreignKey());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_user_relations(): void
    {
        $relations = $this->givenRelations(User::class);

        self::assertNull($relations->get('latestPost'));
        self::assertNull($relations->get('oldestPost'));
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_user_roles_relations(): void
    {
        $relations = $this->givenRelations(User::class);

        /** @var Relation $roles */
        $roles = $relations->get('roles')->firstOrFail();
        self::assertEquals(MorphToMany::class, $roles->type());
        self::assertEquals(Role::class, $roles->related());
        self::assertEquals('users.id', $roles->localKey());
        self::assertEquals('model_has_roles.model_id', $roles->foreignKey());
        self::assertEquals('model_has_roles', $roles->pivot()->table());
        self::assertEquals('model_has_roles.role_id', $roles->pivot()->localKey());
        self::assertEquals('roles.id', $roles->pivot()->foreignKey());
        self::assertEquals('model_type', $roles->pivot()->morphType());
        self::assertEquals(User::class, $roles->pivot()->morphClass());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_user_permissions_relations(): void
    {
        $relations = $this->givenRelations(User::class);

        /** @var Relation $permissions */
        $permissions = $relations->get('permissions')->firstOrFail();
        self::assertEquals(MorphToMany::class, $permissions->type());
        self::assertEquals(Permission::class, $permissions->related());
        self::assertEquals('users.id', $permissions->localKey());
        self::assertEquals('model_has_permissions.model_id', $permissions->foreignKey());
        self::assertEquals('model_has_permissions', $permissions->pivot()->table());
        self::assertEquals('model_has_permissions.permission_id', $permissions->pivot()->localKey());
        self::assertEquals('permissions.id', $permissions->pivot()->foreignKey());
        self::assertEquals('model_type', $permissions->pivot()->morphType());
        self::assertEquals(User::class, $permissions->pivot()->morphClass());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_roles_users_relations(): void
    {
        $relations = $this->givenRelations(Role::class);

        /** @var Relation $users */
        $users = $relations->get('users')->firstOrFail();
        self::assertEquals(MorphToMany::class, $users->type());
        self::assertEquals(AuthUser::class, $users->related());
        self::assertEquals('roles.id', $users->localKey());
        self::assertEquals('model_has_roles.role_id', $users->foreignKey());
        self::assertEquals('model_has_roles', $users->pivot()->table());
        self::assertEquals('model_has_roles.model_id', $users->pivot()->localKey());
        self::assertEquals('users.id', $users->pivot()->foreignKey());
        self::assertEquals('model_type', $users->pivot()->morphType());
        self::assertEquals(AuthUser::class, $users->pivot()->morphClass());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_role_permissions_relations(): void
    {
        $relations = $this->givenRelations(Role::class);

        /** @var Relation $permissions */
        $permissions = $relations->get('permissions')->firstOrFail();
        self::assertEquals(BelongsToMany::class, $permissions->type());
        self::assertEquals(Permission::class, $permissions->related());
        self::assertEquals('roles.id', $permissions->localKey());
        self::assertEquals('role_has_permissions.role_id', $permissions->foreignKey());
        self::assertEquals('role_has_permissions', $permissions->pivot()->table());
        self::assertEquals('role_has_permissions.permission_id', $permissions->pivot()->localKey());
        self::assertEquals('permissions.id', $permissions->pivot()->foreignKey());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_user_imageable_relations(): void
    {
        $relations = $this->givenRelations(User::class);

        /** @var Relation $image */
        $image = $relations->get('image')->firstOrFail();
        self::assertEquals(MorphOne::class, $image->type());
        self::assertEquals(Image::class, $image->related());
        self::assertEquals('users.id', $image->localKey());
        self::assertEquals('images.imageable_id', $image->foreignKey());
        self::assertEquals('images.imageable_type', $image->morphType());
        self::assertEquals(User::class, $image->morphClass());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_image_relations(): void
    {
        $relations = $this->givenRelations(Image::class);

        self::assertCount(0, $relations);
    }

    /**
     * @throws ReflectionException
     */
    public function test_draw_mechanic_car_owner_relations(): void
    {
        $mechanic = $this->givenRelations(Mechanic::class);
        self::assertEquals(['mechanics 1--1 cars'], $this->draw($mechanic, 'car'));

        $cars = $this->givenRelations(Car::class);
        self::assertEquals(['cars 1--1 mechanics'], $this->draw($cars, 'mechanic'));
        self::assertEquals(['cars 1--1 owners'], $this->draw($cars, 'owner'));

        $owner = $this->givenRelations(Owner::class);
        self::assertEquals(['owners 1--1 cars'], $this->draw($owner, 'car'));
    }

    /**
     * @throws ReflectionException
     */
    public function test_draw_user_post_comment_relations(): void
    {
        $user = $this->givenRelations(User::class);
        self::assertEquals(['users 1--* posts'], $this->draw($user, 'posts'));
        self::assertEquals(['users 1--1 images'], $this->draw($user, 'image'));
        self::assertEquals(['users 1--* images'], $this->draw($user, 'images'));

        $post = $this->givenRelations(Post::class);
        self::assertEquals(['posts 1--1 users'], $this->draw($post, 'user'));
        self::assertEquals(['posts 1--* comments'], $this->draw($post, 'comments'));
        self::assertEquals(['posts 1--1 images'], $this->draw($post, 'image'));

        $comment = $this->givenRelations(Comment::class);
        self::assertEquals(['comments 1--1 posts'], $this->draw($comment, 'post'));
    }

    /**
     * @throws ReflectionException
     */
    public function test_draw_user_role_permission_relations(): void
    {
        $user = $this->givenRelations(User::class);
        self::assertEquals([
            'users *--* model_has_roles',
            'model_has_roles *--* roles',
        ], $this->draw($user, 'roles'));
        self::assertEquals([
            'users *--* model_has_permissions',
            'model_has_permissions *--* permissions',
        ], $this->draw($user, 'permissions'));

        $role = $this->givenRelations(Role::class);
        self::assertEquals([
            'roles *--* model_has_roles',
            'model_has_roles *--* users',
        ], $this->draw($role, 'users'));
        self::assertEquals([
            'roles *--* role_has_permissions',
            'role_has_permissions *--* permissions',
        ], $this->draw($role, 'permissions'));

        $permission = $this->givenRelations(Permission::class);
        self::assertEquals([
            'permissions *--* role_has_permissions',
            'role_has_permissions *--* roles',
        ], $this->draw($permission, 'roles'));
        self::assertEquals([
            'permissions *--* model_has_permissions',
            'model_has_permissions *--* users',
        ], $this->draw($permission, 'users'));
        self::assertEquals([
            'permissions *--* model_has_permissions',
            'model_has_permissions *--* permissions',
        ], $this->draw($permission, 'permissions'));
    }

    /**
     * @throws ReflectionException
     */
    private function givenRelations(string $model): Collection
    {
        return $this->finder->generate($model);
    }

    private function draw(Collection $relations, $method): array
    {
        return $relations->get($method)->map(function (Relation $relationship) {
            return $this->renderRelationship($relationship);
        })->toArray();
    }

    private function renderRelationship(Relation $relation): string
    {
        return sprintf(
            '%s %s %s',
            Helpers::getTableName($relation->localKey()),
            self::$relationships[$relation->type()],
            Helpers::getTableName($relation->foreignKey())
        );
    }
}
