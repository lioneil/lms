<?php

namespace User\Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use User\Models\User;
use User\Services\UserServiceInterface;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package User\Tests\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ForbiddenUsersTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(UserServiceInterface::class);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @group  feature:api:user:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_retrieve_the_paginated_list_of_all_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->get(route('api.users.index'));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @group  feature:api:user:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_store_a_user_to_database()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(User::class)->make()->toArray();
        $response = $this->post(route('api.users.store'), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @group  feature:api:user:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_retrieve_a_single_user()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $user = factory(User::class, 2)->create()->random();

        // Actions
        $response = $this->get(route('api.users.show', $user->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @group  feature:api:user:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_update_a_user()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $user = factory(User::class, 2)->create()->random();

        // Actions
        $attributes = factory(User::class)->make()->toArray();
        $response = $this->put(route('api.users.update', $user->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @group  feature:api:user:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_soft_delete_a_user()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $user = factory(User::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.users.destroy', $user->getKey()));
        $user = $this->service->withTrashed()->find($user->getKey());

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @group  feature:api:user:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_soft_delete_multiple_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $users = factory(User::class, 3)->create();

        // Actions
        $attributes = ['id' => $users->pluck('id')->toArray()];
        $response = $this->delete(route('api.users.destroy', 'null'), $attributes);
        $users = $this->service->onlyTrashed();

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @group  feature:api:user:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_retrieve_the_paginated_list_of_all_trashed_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $users = factory(User::class, 2)->create();
        $users->each->delete();

        // Actions
        $response = $this->get(route('api.users.trashed'));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @group  feature:api:user:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_restore_destroyed_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $user = factory(User::class, 3)->create()->random();
        $user->delete();

        // Actions
        $response = $this->patch(route('api.users.restore', $user->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @group  feature:api:user:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_restore_multiple_destroyed_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $users = factory(User::class, 3)->create();
        $users->each->delete();

        // Actions
        $attributes = ['id' => $users->pluck('id')->toArray()];
        $response = $this->patch(route('api.users.restore'), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @group  feature:api:user:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_permanently_delete_a_user()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $user = factory(User::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.users.delete', $user->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:user
     * @group  feature:api:user:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_permanently_delete_multiple_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $users = factory(User::class, 3)->create();

        // Actions
        $attributes = ['id' => $users->pluck('id')->toArray()];
        $response = $this->delete(route('api.users.delete'), $attributes);

        // Assertions
        $response->assertForbidden();
    }

}
