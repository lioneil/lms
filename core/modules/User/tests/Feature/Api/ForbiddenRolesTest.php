<?php

namespace User\Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use User\Models\Role;
use User\Services\RoleServiceInterface;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package User\Tests\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ForbiddenRolesTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(RoleServiceInterface::class);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @group  feature:api:role:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_retrieve_the_paginated_list_of_all_roles()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->get(route('api.roles.index'));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @group  feature:api:role:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_store_a_role_to_database()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Role::class)->make()->toArray();
        $response = $this->post(route('api.roles.store'), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @group  feature:api:role:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_retrieve_a_single_role()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $role = factory(Role::class, 2)->create()->random();

        // Actions
        $response = $this->get(route('api.roles.show', $role->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @group  feature:api:role:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_update_a_role()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $role = factory(Role::class, 2)->create()->random();

        // Actions
        $attributes = factory(Role::class)->make()->toArray();
        $response = $this->put(route('api.roles.update', $role->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @group  feature:api:role:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_soft_delete_a_role()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $role = factory(Role::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.roles.destroy', $role->getKey()));
        $role = $this->service->withTrashed()->find($role->getKey());

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @group  feature:api:role:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_soft_delete_multiple_roles()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $roles = factory(Role::class, 3)->create();

        // Actions
        $attributes = ['id' => $roles->pluck('id')->toArray()];
        $response = $this->delete(route('api.roles.destroy', 'null'), $attributes);
        $roles = $this->service->onlyTrashed();

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @group  feature:api:role:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_retrieve_the_paginated_list_of_all_trashed_roles()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $roles = factory(Role::class, 2)->create();
        $roles->each->delete();

        // Actions
        $response = $this->get(route('api.roles.trashed'));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @group  feature:api:role:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_restore_destroyed_roles()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $role = factory(Role::class, 3)->create()->random();
        $role->delete();

        // Actions
        $response = $this->patch(route('api.roles.restore', $role->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @group  feature:api:role:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_restore_multiple_destroyed_roles()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $roles = factory(Role::class, 3)->create();
        $roles->each->delete();

        // Actions
        $attributes = ['id' => $roles->pluck('id')->toArray()];
        $response = $this->patch(route('api.roles.restore'), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @group  feature:api:role:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_permanently_delete_a_role()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $role = factory(Role::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.roles.delete', $role->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @group  feature:api:role:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_permanently_delete_multiple_roles()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $roles = factory(Role::class, 3)->create();

        // Actions
        $attributes = ['id' => $roles->pluck('id')->toArray()];
        $response = $this->delete(route('api.roles.delete'), $attributes);

        // Assertions
        $response->assertForbidden();
    }

}
