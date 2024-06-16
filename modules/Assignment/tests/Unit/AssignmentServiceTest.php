<?php

namespace Assignment\Unit;

use Assignment\Models\Assignment;
use Assignment\Services\AssignmentServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\User;

/**
 * @package Assignment\Unit
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class AssignmentServiceTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(AssignmentServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
     * @return void
     */
    public function it_can_return_a_paginated_list_of_assignments()
    {
        // Arrangements
        $assignments = factory(Assignment::class, 10)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trashed_assignments()
    {
       // Arrangements
       $assignments = factory(Assignment::class, 10)->create();

       // Actions
       $actual = $this->service->listTrashed();

       // Assertions
       $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
     * @return void
     */
    public function it_can_find_and_return_an_existing_assignment()
    {
        // Arrangements
        $expected = factory(Assignment::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Assignment::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
     * @return void
     */
    public function it_will_abort_to_404_when_an_assignment_does_not_exist()
    {
        // Arrangements
        factory(Assignment::class, 2)->create();

        // Actions
        $this->expectException(ModelNotFoundException::class);
        $actual = $this->service->findOrFail($idThatDoesNotExist = 6);

        // Assertions
        $this->assertNull($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
     * @return void
     */
    public function it_can_update_an_existing_assignment()
    {
        // Arrangements
        $assignment = factory(Assignment::class)->create();

        // Actions
        $attributes = [
            'title' => $title = $this->faker->unique()->words(10, true),
        ];
        $attributes['uri'] = UploadedFile::fake()->create('tmp.text');
        $actual = $this->service->update($assignment->getKey(), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('uri', 'pathname')->toArray());
        $this->assertTrue($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
     * @return void
     */
    public function it_can_restore_a_soft_deleted_assignment()
    {
        // Arrangements
        $assignment = factory(Assignment::class)->create();
        $assignment->delete();

        // Actions
        $actual = $this->service->restore($assignment->getKey());
        $restored = $this->service->find($assignment->getKey());

        // Assertions
        $this->assertNull($actual);
        $this->assertNull($restored->deleted_at);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
     * @return void
     */
    public function it_can_restore_multiple_soft_deleted_assignments()
    {
        // Arrangements
        $assignments = factory(Assignment::class, 5)->create();
        $assignments->each->delete();

        // Actions
        $actual = $this->service->restore($assignments->pluck('id')->toArray());

        // Assertions
        $this->assertNull($actual);
        $assignments->each(function ($assignment) {
            $restored = $this->service->find($assignment->getKey());
            $this->assertNull($restored->deleted_at);
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
     * @return void
     */
    public function it_can_store_an_assignment_to_database()
    {
        // Arrangements
        $attributes = factory(Assignment::class)->make()->toArray();
        $attributes['file'] = UploadedFile::fake()->create('tmp.text');

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('file', 'uri', 'pathname')->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
     * @return void
     */
    public function it_can_soft_delete_an_existing_assignment()
    {
        // Arrangements
        $assignment = factory(Assignment::class, 3)->create()->random();

        // Actions
        $this->service->destroy($assignment->getKey());
        $assignment = $this->service->withTrashed()->find($assignment->getKey());

        // Assertions
        $this->assertSoftDeleted($this->service->getTable(), $assignment->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
     * @return void
     */
    public function it_can_soft_delete_multiple_existing_assignments()
    {
        // Arrangements
        $assignments = factory(Assignment::class, 3)->create();

        // Actions
        $this->service->destroy($assignments->pluck('id')->toArray());
        $assignments = $this->service->withTrashed()->whereIn(
            'id', $assignments->pluck('id')->toArray()
        )->get();

        // Assertions
        $assignments->each(function ($assignment) {
            $this->assertSoftDeleted($this->service->getTable(), $assignment->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_assignment()
    {
        // Arrangements
        $assignment = factory(Assignment::class)->create();
        $assignment->delete();

        // Actions
        $this->service->delete($assignment->getKey());

        // Assertions
        $this->assertDatabaseMissing($this->service->getTable(), $assignment->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
     * @return void
     */
    public function it_can_permanently_delete_multiple_soft_deleted_assignments()
    {
        // Arrangements
        $assignments = factory(Assignment::class, 5)->create();
        $assignments->each->delete();

        // Actions
        $this->service->delete($assignments->pluck('id')->toArray());

        // Assertions
        $assignments->each(function ($assignment) {
            $this->assertDatabaseMissing($this->service->getTable(), $assignment->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
     * @return void
     */
    public function it_should_return_an_array_of_rules()
    {
        // Arrangements
        $rules = $this->service->rules($id = 1);

        // Assertions
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('uri', $rules);
        $this->assertArrayHasKey('user_id', $rules);
        $this->assertEquals('required|max:255', $rules['title']);
        $this->assertEquals('required|numeric', $rules['user_id']);
        $this->assertEquals('required', $rules['uri']);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
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
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
     * @return void
     */
    public function it_can_check_if_user_is_authorized_to_interact_with_assignments()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([]));
        $this->withPermissionsPolicy();
        $restricted = factory(Assignment::class, 3)->create()->random();
        $assignment = factory(Assignment::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $restricted = $this->service->authorize($restricted);
        $authorized = $this->service->authorize($assignment);

        // Assertions
        $this->assertFalse($restricted);
        $this->assertTrue($authorized);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:assignment
     * @group  service
     * @group  service:assignment
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

}
