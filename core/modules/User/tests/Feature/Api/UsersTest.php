<?php

namespace User\Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\Role;
use User\Models\User;
use User\Services\UserServiceInterface;

/**
 * @package User\Tests\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class UsersTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(UserServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @return void
     */
    public function a_user_can_retrieve_the_paginated_list_of_all_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['users.index']), ['users.index']);
        $this->withPermissionsPolicy();

        $users = factory(User::class, 5)->state('test')->create();

        // Actions
        $response = $this->get(route('api.users.index'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJson(['data' => [['type' => 'test']]])
            ->assertJsonStructure([
                'data' => [[
                    'avatar',
                    'displayname',
                    'email',
                    'role',
                    'created',
                    'modified',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @return void
     */
    public function a_user_can_store_a_user_to_database()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        Passport::actingAs($this->asNonSuperAdmin(['users.store']), ['users.store']);
        $this->withPermissionsPolicy();

        // Actions
        // Passwords by default, will be omitted by the User\Models\User model.
        // So we need to make it visible.
        $user = factory(User::class)->make()->makeVisible('password');
        $attributes = array_merge($user->toArray(), [
            'roles' => [Role::pluck('id')->first()],
        ]);
        $response = $this->post(route('api.users.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), $user->makeHidden('password')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @return void
     */
    public function a_user_can_retrieve_a_single_user()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['users.show']), ['users.show']);
        $this->withPermissionsPolicy();
        $user = factory(User::class, 2)->state('test')->create()->random();

        // Actions
        $response = $this->get(route('api.users.show', $user->getKey()));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJson(['data' => ['type' => 'test']])
            ->assertJsonStructure([
                'data' => [
                    'avatar',
                    'displayname',
                    'details',
                    'email',
                    'role',
                    'birthday',
                ],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @return void
     */
    public function a_user_can_update_a_user()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['users.update']), ['users.update']);
        $this->withPermissionsPolicy();
        $original = factory(User::class, 3)->create()->random();

        // Actions
        $user = factory(User::class)->make()->makeVisible('password');
        $attributes = array_merge($user->toArray(), [
            'roles' => [Role::pluck('id')->first()],
        ]);
        $response = $this->put(route('api.users.update', $original->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($user->getTable(), $user->makeHidden('password')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @return void
     */
    public function a_user_can_soft_delete_a_user()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['users.destroy']), ['users.destroy']);
        $this->withPermissionsPolicy();
        $user = factory(User::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.users.destroy', $user->getKey()));
        $user = $this->service->withTrashed()->find($user->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($user->getTable(), $user->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @return void
     */
    public function a_user_can_soft_delete_multiple_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['users.destroy']), ['users.destroy']);
        $this->withPermissionsPolicy();
        $users = factory(User::class, 3)->create();

        // Actions
        $attributes = ['id' => $users->pluck('id')->toArray()];
        $response = $this->delete(route('api.users.destroy', 'null'), $attributes);
        $users = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $users->each(function ($user) {
            $this->assertSoftDeleted($user->getTable(), $user->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @return void
     */
    public function a_user_can_retrieve_the_paginated_list_of_all_trashed_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['users.trashed']), ['users.trashed']);
        $this->withPermissionsPolicy();
        $users = factory(User::class, 2)->state('test')->create();
        $users->each->delete();

        // Actions
        $response = $this->get(route('api.users.trashed'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJson(['data' => [['type' => 'test']]])
            ->assertJsonStructure([
                'data' => [[
                    'avatar',
                    'displayname',
                    'email',
                    'role',
                    'created',
                    'modified',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @return void
     */
    public function a_user_can_restore_destroyed_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['users.restore']), ['users.restore']);
        $this->withPermissionsPolicy();
        $user = factory(User::class, 3)->create()->random();
        $user->delete();

        // Actions
        $response = $this->patch(route('api.users.restore', $user->getKey()));
        $user = $this->service->find($user->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($user->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @return void
     */
    public function a_user_can_restore_multiple_destroyed_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['users.restore']), ['users.restore']);
        $this->withPermissionsPolicy();
        $users = factory(User::class, 3)->create();
        $users->each->delete();

        // Actions
        $attributes = ['id' => $users->pluck('id')->toArray()];
        $response = $this->patch(route('api.users.restore'), $attributes);
        $users = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $users->each(function ($user) {
            $this->assertFalse($user->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @return void
     */
    public function a_user_can_permanently_delete_a_user()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['users.delete']), ['users.delete']);
        $this->withPermissionsPolicy();
        $user = factory(User::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.users.delete', $user->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseMissing($user->getTable(), $user->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @return void
     */
    public function a_user_can_permanently_delete_multiple_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['users.delete']), ['users.delete']);
        $this->withPermissionsPolicy();
        $users = factory(User::class, 3)->create();

        // Actions
        $attributes = ['id' => $users->pluck('id')->toArray()];
        $response = $this->delete(route('api.users.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $users->each(function ($user) {
            $this->assertDatabaseMissing($user->getTable(), $user->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @return void
     */
    public function a_user_is_forbidden_to_permanently_delete_self()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['users.delete']), ['users.delete']);
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->delete(route('api.users.delete', $user->getKey()));
        $user = $this->service->find($user->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($user->getTable(), $user->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @return void
     */
    public function a_user_is_forbidden_to_permanently_delete_self_in_multiple_delete_mode()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['users.delete']), ['users.delete']);
        $this->withPermissionsPolicy();
        $users = factory(User::class, 2)->create();

        // Actions
        $attributes = ['id' => $users->pluck('id')->merge([$user->getKey()])->toArray()];
        $response = $this->delete(route('api.users.delete'), $attributes);
        $user = $this->service->find($user->getKey());

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($user->getTable(), $user->toArray());
        $users->each(function ($user) {
            $this->assertDatabaseHas($user->getTable(), $user->toArray());
        });
    }
}
