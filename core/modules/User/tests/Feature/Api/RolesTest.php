<?php

namespace User\Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\Permission;
use User\Models\Role;
use User\Services\RoleServiceInterface;

/**
 * @package User\Tests\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class RolesTest extends TestCase
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
     * @return void
     */
    public function a_user_can_retrieve_the_paginated_list_of_all_roles()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['roles.index']), ['roles.index']);
        $this->withPermissionsPolicy();

        $roles = factory(Role::class, 5)->create();

        // Actions
        $response = $this->get(route('api.roles.index'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [[
                    'name',
                    'alias',
                    'code',
                    'description',
                    'status',
                    'permissions' => [[
                        'name',
                        'code',
                        'description',
                        'group',
                    ]],
                    'created',
                    'modified',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @return void
     */
    public function a_user_can_store_a_role_to_database()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['roles.store']), ['roles.store']);
        $this->withPermissionsPolicy();

        // Actions
        $role = factory(Role::class)->make();
        $attributes = array_merge($role->toArray(), [
            'permissions' => [$permission = Permission::pluck('id')->first()],
        ]);
        $response = $this->post(route('api.roles.store'), $attributes);
        $role = $this->service->whereCode($role->code)->first();

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($role->getTable(), $role->toArray());
        $this->assertEquals($permission, $role->permissions->first()->getKey());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @return void
     */
    public function a_user_can_retrieve_a_single_role()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        Passport::actingAs($this->asNonSuperAdmin(['roles.show']), ['roles.show']);
        $this->withPermissionsPolicy();
        $role = factory(Role::class, 3)->create()->random();
        $role->permissions()->sync([Permission::pluck('id')->first()]);

        // Actions
        $response = $this->get(route('api.roles.show', $role->getKey()));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJson(['data' => $role->toArray()])
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'alias',
                    'code',
                    'description',
                    'status',
                    'permissions' => [[
                        'name',
                        'code',
                        'description',
                        'group',
                    ]],
                    'created',
                    'modified',
                ],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @return void
     */
    public function a_user_can_update_a_role()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['roles.update']), ['roles.update']);
        $this->withPermissionsPolicy();
        $original = factory(Role::class, 3)->create()->random();

        // Actions
        $role = factory(Role::class)->make();
        $attributes = array_merge($role->toArray(), [
            'permissions' => [Permission::pluck('id')->first()],
        ]);
        $response = $this->put(route('api.roles.update', $original->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($role->getTable(), $role->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @return void
     */
    public function a_user_can_soft_delete_a_role()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['roles.destroy']), ['roles.destroy']);
        $this->withPermissionsPolicy();
        $role = factory(Role::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.roles.destroy', $role->getKey()));
        $role = $this->service->withTrashed()->find($role->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($role->getTable(), $role->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @return void
     */
    public function a_user_can_soft_delete_multiple_roles()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['roles.destroy']), ['roles.destroy']);
        $this->withPermissionsPolicy();
        $roles = factory(Role::class, 3)->create();

        // Actions
        $attributes = ['id' => $roles->pluck('id')->toArray()];
        $response = $this->delete(route('api.roles.destroy', 'null'), $attributes);
        $roles = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $roles->each(function ($role) {
            $this->assertSoftDeleted($role->getTable(), $role->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @return void
     */
    public function a_user_can_retrieve_the_paginated_list_of_all_trashed_roles()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['roles.trashed']), ['roles.trashed']);
        $this->withPermissionsPolicy();
        $roles = factory(Role::class, 2)->create();
        $roles->each->delete();

        // Actions
        $response = $this->get(route('api.roles.trashed'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [[
                    'name',
                    'alias',
                    'code',
                    'description',
                    'status',
                    'created',
                    'modified',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @return void
     */
    public function a_user_can_restore_destroyed_roles()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['roles.restore']), ['roles.restore']);
        $this->withPermissionsPolicy();
        $role = factory(Role::class, 3)->create()->random();
        $role->delete();

        // Actions
        $response = $this->patch(route('api.roles.restore', $role->getKey()));
        $role = $this->service->find($role->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($role->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @return void
     */
    public function a_user_can_restore_multiple_destroyed_roles()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['roles.restore']), ['roles.restore']);
        $this->withPermissionsPolicy();
        $roles = factory(Role::class, 3)->create();
        $roles->each->delete();

        // Actions
        $attributes = ['id' => $roles->pluck('id')->toArray()];
        $response = $this->patch(route('api.roles.restore'), $attributes);
        $roles = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $roles->each(function ($role) {
            $this->assertFalse($role->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @return void
     */
    public function a_user_can_permanently_delete_a_role()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['roles.delete']), ['roles.delete']);
        $this->withPermissionsPolicy();
        $role = factory(Role::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.roles.delete', $role->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseMissing($role->getTable(), $role->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:role
     * @return void
     */
    public function a_user_can_permanently_delete_multiple_roles()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['roles.delete']), ['roles.delete']);
        $this->withPermissionsPolicy();
        $roles = factory(Role::class, 3)->create();

        // Actions
        $attributes = ['id' => $roles->pluck('id')->toArray()];
        $response = $this->delete(route('api.roles.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $roles->each(function ($role) {
            $this->assertDatabaseMissing($role->getTable(), $role->toArray());
        });
    }

}
