<?php

namespace Library\Unit;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Library\Models\Library;
use Library\Services\LibraryServiceInterface;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Library\Unit
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class LibraryServiceTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /* Set up the service class*/
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(LibraryServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
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
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
     * @return void
     */
    public function it_can_return_a_paginated_list_of_libraries()
    {
        // Arrangements
        $libraries = factory(Library::class, 10)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trashed_libraries()
    {
        // Arrangements
        $libraries = factory(Library::class, 10)->create();

        // Actions
        $actual = $this->service->listTrashed();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
     * @return void
     */
    public function it_can_find_and_return_an_existing_library()
    {
        // Arrangements
        $expected = factory(Library::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Library::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
     * @return void
     */
    public function it_will_abort_to_404_when_a_library_does_not_exist()
    {
        // Arrangements
        factory(Library::class, 2)->create();

        // Actions
        $this->expectException(ModelNotFoundException::class);
        $actual = $this->service->findOrFail($idThatDoesNotExist = 6);

        // Assertions
        $this->assertNull($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
     * @return void
     */
    public function it_can_update_an_existing_library()
    {
         // Arrangements
        $library = factory(Library::class)->create();

        // Actions
        $attributes = [
            'name' => $name = $this->faker->name(),
        ];
        $actual = $this->service->update($library->getKey(), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $this->assertTrue($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
     * @return void
     */
    public function it_can_restore_a_soft_deleted_library()
    {
        // Arrangements
        $library = factory(Library::class)->create();
        $library->delete();

        // Actions
        $actual = $this->service->restore($library->getKey());
        $restored = $this->service->find($library->getKey());

        // Assertions
        $this->assertNull($actual);
        $this->assertNull($restored->deleted_at);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
     * @return void
     */
    public function it_can_restore_multiple_soft_deleted_libraries()
    {
        // Arrangements
        $libraries = factory(Library::class, 5)->create();
        $libraries->each->delete();

        // Actions
        $actual = $this->service->restore($libraries->pluck('id')->toArray());

        // Assertions
        $this->assertNull($actual);
        $libraries->each(function ($library) {
            $restored = $this->service->find($library->getKey());
            $this->assertNull($restored->deleted_at);
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
     * @return void
     */
    public function it_can_store_a_library_to_database()
    {
        // Arrangements
        $attributes = factory(Library::class)->make()->toArray();

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
     * @return void
     */
    public function it_can_soft_delete_an_existing_library()
    {
        // Arrangements
        $library = factory(Library::class, 3)->create()->random();

        // Actions
        $this->service->destroy($library->getKey());

        // Assertions
        $this->assertSoftDeleted($this->service->getTable(), $library->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
     * @return void
     */
    public function it_can_soft_delete_multiple_existing_libraries()
    {
        // Arrangements
        $libraries = factory(Library::class, 3)->create();

        // Actions
        $this->service->destroy($libraries->pluck('id')->toArray());

        // Assertions
        $libraries->each(function ($library) {
            $this->assertSoftDeleted($this->service->getTable(), $library->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_library()
    {
        // Arrangements
        $library = factory(Library::class)->create();
        $library->delete();

        // Actions
        $this->service->delete($library->getKey());

        // Assertions
        $this->assertDatabaseMissing($this->service->getTable(), $library->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
     * @return void
     */
    public function it_can_permanently_delete_multiple_soft_deleted_libraries()
    {
        // Arrangements
        $libraries = factory(Library::class, 5)->create();
        $libraries->each->delete();

        // Actions
        $this->service->delete($libraries->pluck('id')->toArray());

        // Assertions
        $libraries->each(function ($library) {
            $this->assertDatabaseMissing($this->service->getTable(), $library->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
     * @return void
     */
    public function it_should_return_an_array_of_rules()
    {
        // Arrangements
        $rules = $this->service->rules($id = 1);

        // Assertions
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('name', $rules);
        $this->assertEquals('required|max:255', $rules['name']);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:library
     * @group  service
     * @group  service:library
     * @return void
     */
    public function it_should_return_an_array_of_messages()
    {
        // Arrangements
        $messages = $this->service->messages();

        // Assertions
        $this->assertIsArray($messages);
    }
}
