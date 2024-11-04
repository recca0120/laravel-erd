<?php

namespace Recca0120\LaravelErd\Tests;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Recca0120\LaravelErd\Relation;
use Recca0120\LaravelErd\RelationFinder;
use Recca0120\LaravelErd\Tests\Fixtures\Models\Car;
use Recca0120\LaravelErd\Tests\Fixtures\Models\Comment;
use Recca0120\LaravelErd\Tests\Fixtures\Models\Image;
use Recca0120\LaravelErd\Tests\Fixtures\Models\Mechanic;
use Recca0120\LaravelErd\Tests\Fixtures\Models\Owner;
use Recca0120\LaravelErd\Tests\Fixtures\Models\Post;
use Recca0120\LaravelErd\Tests\Fixtures\Models\User;
use ReflectionException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RelationFinderTest extends TestCase
{
    use RefreshDatabase;

    private static array $relationships = [
        BelongsTo::class => '1--*',
        MorphTo::class => '1--*',
        HasOne::class => '1--1',
        MorphOne::class => '1--1',
        HasMany::class => '1--*',
        MorphMany::class => '1--*',
        BelongsToMany::class => '1--*',
        MorphToMany::class => '1--*',
    ];

    /**
     * @throws ReflectionException
     */
    public function test_find_mechanic_relations(): void
    {
        $relations = $this->givenRelations(Mechanic::class);

        /** @var Relation $relation */
        $relation = $relations->get('car')->firstOrFail();
        self::assertEquals(HasOne::class, $relation->type());
        self::assertEquals(Car::class, $relation->related());
        self::assertEquals(Mechanic::class, $relation->parent());
        self::assertEquals(['mechanics.id'], $relation->localKeys());
        self::assertEquals(['cars.mechanic_id'], $relation->foreignKeys());

        $relation = $relation->relatedRelation();
        self::assertEquals(BelongsTo::class, $relation->type());
        self::assertEquals(Mechanic::class, $relation->related());
        self::assertEquals(Car::class, $relation->parent());
        self::assertEquals(['cars.mechanic_id'], $relation->localKeys());
        self::assertEquals(['mechanics.id'], $relation->foreignKeys());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_car_relations(): void
    {
        $relations = $this->givenRelations(Car::class);

        /** @var Relation $relation */
        $relation = $relations->get('mechanic')->firstOrFail();
        self::assertEquals(BelongsTo::class, $relation->type());
        self::assertEquals(Mechanic::class, $relation->related());
        self::assertEquals(Car::class, $relation->parent());
        self::assertEquals(['cars.mechanic_id'], $relation->localKeys());
        self::assertEquals(['mechanics.id'], $relation->foreignKeys());

        $relation = $relation->relatedRelation();
        self::assertEquals(HasMany::class, $relation->type());
        self::assertEquals(Car::class, $relation->related());
        self::assertEquals(Mechanic::class, $relation->parent());
        self::assertEquals(['mechanics.id'], $relation->localKeys());
        self::assertEquals(['cars.mechanic_id'], $relation->foreignKeys());

        /** @var Relation $relation */
        $relation = $relations->get('owner')->firstOrFail();
        self::assertEquals(HasOne::class, $relation->type());
        self::assertEquals(Owner::class, $relation->related());
        self::assertEquals(Car::class, $relation->parent());
        self::assertEquals(['cars.id'], $relation->localKeys());
        self::assertEquals(['owners.car_id'], $relation->foreignKeys());

        $relation = $relation->relatedRelation();
        self::assertEquals(BelongsTo::class, $relation->type());
        self::assertEquals(Car::class, $relation->related());
        self::assertEquals(Owner::class, $relation->parent());
        self::assertEquals(['owners.car_id'], $relation->localKeys());
        self::assertEquals(['cars.id'], $relation->foreignKeys());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_owner_relations(): void
    {
        $relations = $this->givenRelations(Owner::class);

        /** @var Relation $relation */
        $relation = $relations->get('car')->firstOrFail();
        self::assertEquals(BelongsTo::class, $relation->type());
        self::assertEquals(Car::class, $relation->related());
        self::assertEquals(Owner::class, $relation->parent());
        self::assertEquals(['owners.car_id'], $relation->localKeys());
        self::assertEquals(['cars.id'], $relation->foreignKeys());

        $relation = $relation->relatedRelation();
        self::assertEquals(HasMany::class, $relation->type());
        self::assertEquals(Owner::class, $relation->related());
        self::assertEquals(Car::class, $relation->parent());
        self::assertEquals(['cars.id'], $relation->localKeys());
        self::assertEquals(['owners.car_id'], $relation->foreignKeys());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_post_relations(): void
    {
        $relations = $this->givenRelations(Post::class);

        /** @var Relation $relation */
        $relation = $relations->get('comments')->firstOrFail();
        self::assertEquals(HasMany::class, $relation->type());
        self::assertEquals(Comment::class, $relation->related());
        self::assertEquals(Post::class, $relation->parent());
        self::assertEquals(['posts.id'], $relation->localKeys());
        self::assertEquals(['comments.post_id'], $relation->foreignKeys());

        $relation = $relation->relatedRelation();
        self::assertEquals(BelongsTo::class, $relation->type());
        self::assertEquals(Post::class, $relation->related());
        self::assertEquals(Comment::class, $relation->parent());
        self::assertEquals(['comments.post_id'], $relation->localKeys());
        self::assertEquals(['posts.id'], $relation->foreignKeys());

        /** @var Relation $relation */
        $relation = $relations->get('user')->firstOrFail();
        self::assertEquals(BelongsTo::class, $relation->type());
        self::assertEquals(User::class, $relation->related());
        self::assertEquals(Post::class, $relation->parent());
        self::assertEquals(['posts.user_id'], $relation->localKeys());
        self::assertEquals(['users.id'], $relation->foreignKeys());

        $relation = $relation->relatedRelation();
        self::assertEquals(HasMany::class, $relation->type());
        self::assertEquals(Post::class, $relation->related());
        self::assertEquals(User::class, $relation->parent());
        self::assertEquals(['users.id'], $relation->localKeys());
        self::assertEquals(['posts.user_id'], $relation->foreignKeys());
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

        /** @var Relation $relation */
        $relation = $relations->get('roles')->firstOrFail();
        self::assertEquals(MorphToMany::class, $relation->type());
        self::assertEquals(Role::class, $relation->related());
        self::assertEquals(User::class, $relation->parent());
        self::assertEquals(['users.id'], $relation->localKeys());
        self::assertEquals(['model_has_roles.model_id'], $relation->foreignKeys());

        $pivot = $relation->pivot();
        self::assertEquals(MorphTo::class, $pivot->type());
        self::assertEquals('model_has_roles', $pivot->localTable());
        self::assertEquals('model_has_roles.role_id', $pivot->localKey());
        self::assertEquals('roles', $pivot->foreignTable());
        self::assertEquals('roles.id', $pivot->foreignKey());
        self::assertEquals('model_type', $pivot->morphType());
        self::assertEquals(User::class, $pivot->morphClass());

        $relation = $relation->relatedRelation();
        self::assertEquals(MorphToMany::class, $relation->type());
        self::assertEquals(User::class, $relation->related());
        self::assertEquals(Role::class, $relation->parent());
        self::assertEquals(['model_has_roles.model_id'], $relation->localKeys());
        self::assertEquals(['users.id'], $relation->foreignKeys());

        $pivot = $relation->pivot();
        self::assertEquals(MorphTo::class, $pivot->type());
        self::assertEquals('model_has_roles', $pivot->localTable());
        self::assertEquals('model_has_roles.role_id', $pivot->localKey());
        self::assertEquals('roles', $pivot->foreignTable());
        self::assertEquals('roles.id', $pivot->foreignKey());
        self::assertEquals('model_type', $pivot->morphType());
        self::assertEquals(User::class, $pivot->morphClass());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_user_permissions_relations(): void
    {
        $relations = $this->givenRelations(User::class);

        /** @var Relation $relation */
        $relation = $relations->get('permissions')->firstOrFail();
        self::assertEquals(MorphToMany::class, $relation->type());
        self::assertEquals(Permission::class, $relation->related());
        self::assertEquals(User::class, $relation->parent());
        self::assertEquals(['users.id'], $relation->localKeys());
        self::assertEquals(['model_has_permissions.model_id'], $relation->foreignKeys());

        $pivot = $relation->pivot();
        self::assertEquals(MorphTo::class, $pivot->type());
        self::assertEquals('model_has_permissions', $pivot->localTable());
        self::assertEquals('model_has_permissions.permission_id', $pivot->localKey());
        self::assertEquals('permissions', $pivot->foreignTable());
        self::assertEquals('permissions.id', $pivot->foreignKey());
        self::assertEquals('model_type', $pivot->morphType());
        self::assertEquals(User::class, $pivot->morphClass());

        $relation = $relation->relatedRelation();
        self::assertEquals(MorphToMany::class, $relation->type());
        self::assertEquals(User::class, $relation->related());
        self::assertEquals(Permission::class, $relation->parent());
        self::assertEquals(['model_has_permissions.model_id'], $relation->localKeys());
        self::assertEquals(['users.id'], $relation->foreignKeys());

        $pivot = $relation->pivot();
        self::assertEquals(MorphTo::class, $pivot->type());
        self::assertEquals('model_has_permissions', $pivot->localTable());
        self::assertEquals('model_has_permissions.permission_id', $pivot->localKey());
        self::assertEquals('permissions', $pivot->foreignTable());
        self::assertEquals('permissions.id', $pivot->foreignKey());
        self::assertEquals('model_type', $pivot->morphType());
        self::assertEquals(User::class, $pivot->morphClass());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_roles_users_relations(): void
    {
        $relations = $this->givenRelations(Role::class);

        /** @var Relation $relation */
        $relation = $relations->get('users')->firstOrFail();
        self::assertEquals(MorphToMany::class, $relation->type());
        self::assertEquals(AuthUser::class, $relation->related());
        self::assertEquals(Role::class, $relation->parent());
        self::assertEquals(['roles.id'], $relation->localKeys());
        self::assertEquals(['model_has_roles.role_id'], $relation->foreignKeys());

        $pivot = $relation->pivot();
        self::assertEquals(MorphTo::class, $pivot->type());
        self::assertEquals('model_has_roles', $pivot->localTable());
        self::assertEquals('model_has_roles.model_id', $pivot->localKey());
        self::assertEquals('users', $pivot->foreignTable());
        self::assertEquals('users.id', $pivot->foreignKey());
        self::assertEquals('model_type', $pivot->morphType());
        self::assertEquals(AuthUser::class, $pivot->morphClass());

        $relation = $relation->relatedRelation();
        self::assertEquals(MorphToMany::class, $relation->type());
        self::assertEquals(Role::class, $relation->related());
        self::assertEquals(AuthUser::class, $relation->parent());
        self::assertEquals(['model_has_roles.role_id'], $relation->localKeys());
        self::assertEquals(['roles.id'], $relation->foreignKeys());

        $pivot = $relation->pivot();
        self::assertEquals(MorphTo::class, $pivot->type());
        self::assertEquals('model_has_roles', $pivot->localTable());
        self::assertEquals('model_has_roles.model_id', $pivot->localKey());
        self::assertEquals('users', $pivot->foreignTable());
        self::assertEquals('users.id', $pivot->foreignKey());
        self::assertEquals('model_type', $pivot->morphType());
        self::assertEquals(AuthUser::class, $pivot->morphClass());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_role_permissions_relations(): void
    {
        $relations = $this->givenRelations(Role::class);

        /** @var Relation $relation */
        $relation = $relations->get('permissions')->firstOrFail();
        self::assertEquals(BelongsToMany::class, $relation->type());
        self::assertEquals(Permission::class, $relation->related());
        self::assertEquals(Role::class, $relation->parent());
        self::assertEquals(['roles.id'], $relation->localKeys());
        self::assertEquals(['role_has_permissions.role_id'], $relation->foreignKeys());

        $pivot = $relation->pivot();
        self::assertEquals(BelongsTo::class, $pivot->type());
        self::assertEquals('role_has_permissions', $pivot->localTable());
        self::assertEquals('role_has_permissions.permission_id', $pivot->localKey());
        self::assertEquals('permissions', $pivot->foreignTable());
        self::assertEquals('permissions.id', $pivot->foreignKey());

        $relation = $relation->relatedRelation();
        self::assertEquals(BelongsToMany::class, $relation->type());
        self::assertEquals(Role::class, $relation->related());
        self::assertEquals(Permission::class, $relation->parent());
        self::assertEquals(['role_has_permissions.role_id'], $relation->localKeys());
        self::assertEquals(['roles.id'], $relation->foreignKeys());

        $pivot = $relation->pivot();
        self::assertEquals(BelongsTo::class, $pivot->type());
        self::assertEquals('role_has_permissions', $pivot->localTable());
        self::assertEquals('role_has_permissions.permission_id', $pivot->localKey());
        self::assertEquals('permissions', $pivot->foreignTable());
        self::assertEquals('permissions.id', $pivot->foreignKey());
    }

    /**
     * @throws ReflectionException
     */
    public function test_find_user_imageable_relations(): void
    {
        $relations = $this->givenRelations(User::class);

        /** @var Relation $relation */
        $relation = $relations->get('image')->firstOrFail();
        self::assertEquals(MorphOne::class, $relation->type());
        self::assertEquals(Image::class, $relation->related());
        self::assertEquals(User::class, $relation->parent());
        self::assertEquals(['users.id'], $relation->localKeys());
        self::assertEquals(['images.imageable_id'], $relation->foreignKeys());
        self::assertEquals('images.imageable_type', $relation->morphType());
        self::assertEquals(User::class, $relation->morphClass());

        $relation = $relation->relatedRelation();
        self::assertEquals(MorphTo::class, $relation->type());
        self::assertEquals(User::class, $relation->related());
        self::assertEquals(Image::class, $relation->parent());
        self::assertEquals(['images.imageable_id'], $relation->localKeys());
        self::assertEquals(['users.id'], $relation->foreignKeys());
        self::assertEquals('images.imageable_type', $relation->morphType());
        self::assertEquals(User::class, $relation->morphClass());
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
        self::assertEquals(['cars 1--* mechanics'], $this->draw($cars, 'mechanic'));
        self::assertEquals(['cars 1--1 owners'], $this->draw($cars, 'owner'));

        $owner = $this->givenRelations(Owner::class);
        self::assertEquals(['owners 1--* cars'], $this->draw($owner, 'car'));
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
        self::assertEquals(['posts 1--* users'], $this->draw($post, 'user'));
        self::assertEquals(['posts 1--* comments'], $this->draw($post, 'comments'));
        self::assertEquals(['posts 1--1 images'], $this->draw($post, 'image'));

        $comment = $this->givenRelations(Comment::class);
        self::assertEquals(['comments 1--* posts'], $this->draw($comment, 'post'));
    }

    /**
     * @throws ReflectionException
     */
    public function test_draw_user_role_permission_relations(): void
    {
        $user = $this->givenRelations(User::class);
        self::assertEquals([
            'users 1--* model_has_roles',
            'model_has_roles 1--* roles',
        ], $this->draw($user, 'roles'));
        self::assertEquals([
            'users 1--* model_has_permissions',
            'model_has_permissions 1--* permissions',
        ], $this->draw($user, 'permissions'));

        $role = $this->givenRelations(Role::class);
        self::assertEquals([
            'roles 1--* model_has_roles',
            'model_has_roles 1--* users',
        ], $this->draw($role, 'users'));
        self::assertEquals([
            'roles 1--* role_has_permissions',
            'role_has_permissions 1--* permissions',
        ], $this->draw($role, 'permissions'));

        $permission = $this->givenRelations(Permission::class);
        self::assertEquals([
            'permissions 1--* role_has_permissions',
            'role_has_permissions 1--* roles',
        ], $this->draw($permission, 'roles'));
        self::assertEquals([
            'permissions 1--* model_has_permissions',
            'model_has_permissions 1--* users',
        ], $this->draw($permission, 'users'));
        self::assertEquals([
            'permissions 1--* model_has_permissions',
            'model_has_permissions 1--* permissions',
        ], $this->draw($permission, 'permissions'));
    }

    /**
     * @return Collection<string, Collection<int, Relation>>>
     *
     * @throws ReflectionException
     */
    private function givenRelations(string $model): Collection
    {
        return (new RelationFinder)->generate($model);
    }

    private function draw(Collection $relations, $method): array
    {
        return $relations
            ->get($method)
            ->sortBy(fn (Relation $relation) => $relation->sortByRelation())
            ->map(fn (Relation $relation) => $this->renderRelationship($relation))
            ->toArray();
    }

    private function renderRelationship(Relation $relation): string
    {
        return sprintf(
            '%s %s %s',
            $relation->localTable(),
            self::$relationships[$relation->type()],
            $relation->foreignTable()
        );
    }
}
