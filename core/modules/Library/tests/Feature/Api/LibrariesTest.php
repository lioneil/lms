<?php

namespace Library\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Library\Models\Library;
use Library\Services\LibraryServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Library\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class LibrariesTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(LibraryServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:library
     * @return void
     */
    public function a_user_can_only_retrieve_their_owned_paginated_list_of_libraries()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['libraries.index']), ['libraries.index']);
        $this->withPermissionsPolicy();

        $library = factory(Library::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $response = $this->get(route('api.libraries.index'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [[
                    'name', 'url', 'pathname',
                    'size', 'type', 'user_id',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:libraries
     * @return void
     */
    public function a_user_can_store_a_library_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['libraries.store']), ['libraries.store']);
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Library::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('api.libraries.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('metadata')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:library
     * @return void
     */
    public function a_user_can_only_retrieve_an_owned_single_library()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['libraries.show']), ['libraries.show']);
        $this->withPermissionsPolicy();

        $library = factory(Library::class, 2)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('api.libraries.show', $library->getKey()));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'name', 'url', 'pathname',
                    'size', 'type', 'user_id',
                ],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:library
     * @return void
     */
    public function a_user_can_only_update_an_owned_library()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['libraries.update']), ['libraries.update']);
        $this->withPermissionsPolicy();

        $library = factory(Library::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = factory(Library::class)->make()->toArray();

        $response = $this->put(route('api.libraries.update', $library->getKey()), $attributes);
        $library = $this->service->find($library->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($library->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:library
     * @return void
     */
    public function a_user_can_only_soft_delete_an_owned_library()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['libraries.destroy']), ['libraries.destroy']);
        $this->withPermissionsPolicy();

        $library = factory(Library::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.libraries.destroy', $library->getKey()));
        $library = $this->service->withTrashed()->find($library->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($library->getTable(), $library->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:library
     * @return void
     */
    public function a_user_can_only_soft_delete_multiple_owned_libraries()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['libraries.destroy']), ['libraries.destroy']);
        $this->withPermissionsPolicy();

        $libraries = factory(Library::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = ['id' => $libraries->pluck('id')->toArray()];
        $response = $this->delete(route('api.libraries.destroy', 'null'), $attributes);
        $libraries = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $libraries->each(function ($library) {
            $this->assertSoftDeleted($library->getTable(), $library->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:library
     * @return void
     */
    public function a_user_can_only_retrieve_their_owned_paginated_list_of_trashed_libraries()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['libraries.trashed']), ['libraries.trashed']);
        $this->withPermissionsPolicy();

        $libraries = factory(Library::class, 2)->create(['user_id' => $user->getKey()]);
        $libraries->each->delete();

        // Actions
        $response = $this->get(route('api.libraries.trashed'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [[
                    'name', 'url', 'pathname',
                    'size', 'type', 'user_id',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:library
     * @return void
     */
    public function a_user_can_only_restore_owned_destroyed_library()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['libraries.restore']), ['libraries.restore']);
        $this->withPermissionsPolicy();

        $library = factory(Library::class, 3)->create(['user_id' => $user->getKey()])->random();
        $library->delete();

        // Actions
        $response = $this->patch(route('api.libraries.restore', $library->getKey()));
        $library = $this->service->find($library->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($library->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:libraries
     * @return void
     */
    public function a_user_can_only_restore_multiple_owned_destroyed_libraries()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['libraries.restore']), ['libraries.restore']);
        $this->withPermissionsPolicy();

        $libraries = factory(Library::class, 3)->create(['user_id' => $user->getKey()]);
        $libraries->each->delete();

        // Actions
        $attributes = ['id' => $libraries->pluck('id')->toArray()];
        $response = $this->patch(route('api.libraries.restore'), $attributes);
        $libraries = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $libraries->each(function ($library) {
            $this->assertFalse($library->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:library
     * @return void
     */
    public function a_user_can_only_permanently_delete_multiple_owned_libraries()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['libraries.delete']), ['libraries.delete']);
        $this->withPermissionsPolicy();

        $libraries = factory(Library::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = ['id' => $libraries->pluck('id')->toArray()];
        $response = $this->delete(route('api.libraries.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $libraries->each(function ($library) {
            $this->assertDatabaseMissing($library->getTable(), $library->toArray());
        });
    }
}
