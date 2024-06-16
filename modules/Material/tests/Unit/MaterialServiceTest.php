<?php

namespace Tests\Material\Unit;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Material\Models\Material;
use Material\Services\MaterialServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use Illuminate\Validation\Rule;
use User\Models\User;

/**
 * @package Material\Unit
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class MaterialServiceTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;


     /* Set up the service class*/
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(MaterialServiceInterface::class);
    }


    /**
     * A basic test example.
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_should_return_the_url_when_uploading_the_file()
    {
        //Arrangements
        $fakeFile = UploadedFile::fake()->create('tmp.txt', 20);

        //Actions
        $actual = $this->service->upload($fakeFile);

        //Assertions
        $this->assertIsString($actual);
    }

    /**
     * Browse
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_can_return_a_paginated_list_of_materials()
    {
        // Arrangements
        $materials = factory(Material::class, 10)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * Browse
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trashed_materials()
    {
        // Arrangements
        $materials = factory(Material::class, 10)->create();

        // Actions
        $actual = $this->service->listTrashed();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * Read
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_can_find_and_return_an_existing_material()
    {
        // Arrangements
        $expected = factory(Material::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Material::class, $actual);
    }

    /**
     * Read
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_will_abort_to_404_when_a_material_does_not_exist()
    {
        // Arrangements
        factory(Material::class, 2)->create();

        // Actions
        $this->expectException(ModelNotFoundException::class);
        $actual = $this->service->findOrFail($idThatDoesNotExist = 6);

        // Assertions
        $this->assertNull($actual);
    }

    /**
     * Edit
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_can_update_an_existing_material()
    {
        // Arrangements
        $material = factory(Material::class)->create();

        // Actions
        $attributes = [
            'title' => $title = $this->faker->unique()->words(10, true),
        ];

        $attributes['uri'] = UploadedFile::fake()->create('tmp.text');
        $actual = $this->service->update($material->getKey(), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('uri', 'pathname')->
            toArray());
        $this->assertTrue($actual);
    }

    /**
     * Edit
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_can_restore_a_soft_deleted_material()
    {
        // Arrangements
        $material = factory(Material::class)->create();
        $material->delete();

        // Actions
        $actual = $this->service->restore($material->getKey());
        $restored = $this->service->find($material->getKey());

        // Assertions
        $this->assertNull($actual);
        $this->assertNull($restored->deleted_at);
    }

    /**
     * Edit
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_can_restore_multiple_soft_deleted_materials()
    {
        // Arrangements
        $materials = factory(Material::class, 5)->create();
        $materials->each->delete();

        // Actions
        $actual = $this->service->restore($materials->pluck('id')->toArray());

        // Assertions
        $this->assertNull($actual);
        $materials->each(function ($material) {
            $restored = $this->service->find($material->getKey());
            $this->assertNull($restored->deleted_at);
        });
    }

    /**
     * Add
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_can_store_a_material_to_database()
    {
        // Arrangements
        $attributes = factory(Material::class)->make()->toArray();
        $attributes['file'] = UploadedFile::fake()->create('tmp.text');

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('file','uri','pathname')->toArray());
    }

    /**
     * Delete
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_can_soft_delete_an_existing_material()
    {
        // Arrangements
        $material = factory(Material::class, 3)->create()->random();

        // Actions
        $this->service->destroy($material->getKey());
        $material = $this->service->withTrashed()->find($material->getKey());

        // Assertions
        $this->assertSoftDeleted($this->service->getTable(), $material->toArray());
    }

    /**
     * Delete
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_can_soft_delete_multiple_existing_materials()
    {
        // Arrangements
        $materials = factory(Material::class, 3)->create();

        // Actions
        $this->service->destroy($materials->pluck('id')->toArray());
        $materials = $this->service->withTrashed()->whereIn(
            'id', $materials->pluck('id')->toArray()
        );

        // Assertions
        $materials->each(function ($material) {
            $this->assertSoftDeleted($this->service->getTable(), $material->toArray());
        });
    }

    /**
     * Delete
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_material()
    {
        // Arrangements
        $material = factory(Material::class)->create();
        $material->delete();

        // Actions
        $this->service->delete($material->getKey());

        // Assertions
        $this->assertDatabaseMissing($this->service->getTable(), $material->toArray());
    }

    /**
     * Delete
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_can_permanently_delete_multiple_soft_deleted_materials()
    {
        // Arrangements
        $materials = factory(Material::class, 5)->create();
        $materials->each->delete();

        // Actions
        $this->service->delete($materials->pluck('id')->toArray());

        // Assertions
        $materials->each(function ($material) {
            $this->assertDatabaseMissing($this->service->getTable(), $material->toArray());
        });
    }

    /**
     * Rules
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_should_return_an_array_of_rules()
    {
        // Arrangements
        $rules = $this->service->rules($id = 1);

        // Assertions
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('user_id', $rules);
        $this->assertEquals('required|max:255', $rules['title']);
        $this->assertEquals('required|numeric', $rules['user_id']);
        $this->assertEquals('required', $rules['uri']);
    }


    /**
     * Rules
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_should_return_an_array_of_messages()
    {
        // Arrangements
        $messages = $this->service->messages();

        // Assertions
        $this->assertIsArray($messages);
    }

    /**
     * Authorization
     *
     * @test
     * @group  unit
     * @group  unit:material
     * @group  service
     * @group  service:material
     * @return void
     */
    public function it_can_check_if_user_is_authorized_to_interact_with_materials()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([]));
        $this->withPermissionsPolicy();
        $restricted = factory(Material::class, 3)->create()->random();
        $material = factory(Material::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $restricted = $this->service->authorize($restricted);
        $authorized = $this->service->authorize($material);

        // Assertions
        $this->assertFalse($restricted);
        $this->assertTrue($authorized);
    }

}
