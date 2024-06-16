<?php

namespace Material\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Material\Models\Material;
use Material\Services\MaterialServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithForeignKeys;
use Tests\WithPermissionsPolicy;


/**
 * @package Material\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class MaterialsOwnedTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy, WithForeignKeys;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(MaterialServiceInterface::class);

    }

     /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:material
     * @return void
     */
    public function a_user_can_only_retrieve_their_owned_paginated_list_of_materials()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.index']), ['materials.index']);
        $this->withPermissionsPolicy();

        $material = factory(Material::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $response = $this->get(route('api.materials.index',));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [[
                    'title','uri','pathname','coursewareable_id','coursewareable_type','type','user_id',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:materials
     * @return void
     */
    public function a_user_can_store_a_material_to_database()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.store']), ['materials.store']);
        $this->withPermissionsPolicy();

        // Actions
        // $attributes = factory(Material::class)->make(['user_id' => $user->getKey()]);
        $attributes = factory(Material::class)->make(['user_id' => $user->getKey()])->toArray();
        $attributes['file'] = UploadedFile::fake()->create('tmp.text');
        $response = $this->post(route('api.materials.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        // $this->assertDatabaseHas($this->service->getTable(), $attributes->toArray());
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('file','uri','pathname')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:material
     * @return void
     */
    public function a_user_can_only_retrieve_an_owned_single_material()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.show']), ['materials.show']);
        $this->withPermissionsPolicy();

        $material = factory(Material::class, 2)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('api.materials.show', $material->getKey()));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'title','uri','pathname','coursewareable_id','coursewareable_type','type','user_id',
                ],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:material
     * @return void
     */
    public function a_user_can_only_update_an_owned_material()
    {
        // Arrangements
        // $this->withoutExceptionHandling();
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.update']), ['materials.update']);
        $this->withPermissionsPolicy();

        $material = factory(Material::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        // $attributes = factory(Material::class)->make(['user_id' => $user->getKey()]);
        $attributes = factory(Material::class)->make()->toArray();
        $attributes['file'] = UploadedFile::fake()->create('tmp.text');
        $response = $this->put(route('api.materials.update', $material->getKey()), $attributes);
        $material = $this->service->find($material->getKey());
        // dd($attributes->toArray(),$material->toArray());
        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($material->getTable(), collect($attributes)->except('file', 'uri', 'pathname')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:material
     * @return void
     */
    public function a_user_can_only_soft_delete_an_owned_material()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.destroy']), ['materials.destroy']);
        $this->withPermissionsPolicy();

        $material = factory(Material::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.materials.destroy', $material->getKey()));
        $material = $this->service->withTrashed()->find($material->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($material->getTable(), $material->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:material
     * @return void
     */
    public function a_user_can_only_soft_delete_multiple_owned_materials()
    {
        // Arrangements
        $this->withoutExceptionHandling();
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.destroy']), ['materials.destroy']);
        $this->withPermissionsPolicy();

        $materials = factory(Material::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = ['id' => $materials->pluck('id')->toArray()];
        $response = $this->delete(route('api.materials.destroy', 'null'), $attributes);
        $materials = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $materials->each(function ($material) {
            $this->assertSoftDeleted($material->getTable(), $material->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:material
     * @return void
     */
    public function a_user_can_only_retrieve_their_owned_paginated_list_of_trashed_materials()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.trashed']), ['materials.trashed']);
        $this->withPermissionsPolicy();

        $materials = factory(Material::class, 2)->create(['user_id' => $user->getKey()]);
        $materials->each->delete();

        // Actions
        $response = $this->get(route('api.materials.trashed'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [[
                    'title','uri','pathname','coursewareable_id','coursewareable_type','type','user_id',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:material
     * @return void
     */
    public function a_user_can_only_restore_owned_destroyed_material()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.restore']), ['materials.restore']);
        $this->withPermissionsPolicy();

        $material = factory(Material::class, 3)->create(['user_id' => $user->getKey()])->random();
        $material->delete();

        // Actions
        $response = $this->patch(route('api.materials.restore', $material->getKey()));
        $material = $this->service->find($material->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($material->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:materials
     * @return void
     */
    public function a_user_can_only_restore_multiple_owned_destroyed_materials()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.restore']), ['materials.restore']);
        $this->withPermissionsPolicy();

        $materials = factory(Material::class, 3)->create(['user_id' => $user->getKey()]);
        $materials->each->delete();

        // Actions
        $attributes = ['id' => $materials->pluck('id')->toArray()];
        $response = $this->patch(route('api.materials.restore'), $attributes);
        $materials = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $materials->each(function ($material) {
            $this->assertFalse($material->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:material
     * @return void
     */
    public function a_user_can_only_permanently_delete_multiple_owned_materials()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['materials.delete']), ['materials.delete']);
        $this->withPermissionsPolicy();

        $materials = factory(Material::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = ['id' => $materials->pluck('id')->toArray()];
        $response = $this->delete(route('api.materials.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $materials->each(function ($material) {
            $this->assertDatabaseMissing($material->getTable(), $material->toArray());
        });
    }

}
