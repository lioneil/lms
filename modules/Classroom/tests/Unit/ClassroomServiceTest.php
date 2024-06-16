<?php

namespace Classroom\Unit;

use Classroom\Models\Classroom;
use Classroom\Services\ClassroomServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\User;

/**
 * @package Classroom\Unit
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ClassroomServiceTest extends TestCase
{
    use ActingAsUser, DatabaseMigrations, RefreshDatabase, WithFaker, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ClassroomServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_can_return_a_paginated_list_of_classrooms()
    {
        // Arrangements
        $classrooms = factory(Classroom::class, 5)->create();

        // Actions
        $actual = $this->service->list();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trashed_classrooms()
    {
        // Arrangements
        $classrooms = factory(Classroom::class, 5)->create();
        $classrooms->each->delete();

        // Actions
        $actual = $this->service->listTrashed();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_can_find_and_return_an_existing_classroom()
    {
        // Arrangements
        $expected = factory(Classroom::class, 5)->create();

        // Actions
        $actual = $this->service->find($expected->random()->getKey());

        // Assertions
        $this->assertInstanceOf(Classroom::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_will_abort_to_404_when_a_classroom_does_not_exist()
    {
        // Arrangements
        factory(Classroom::class, 2)->create();

        // Actions
        $this->expectException(ModelNotFoundException::class);
        $actual = $this->service->findOrFail($idThatDoesNotExist = 6);

        // Assertions
        $this->assertNull($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_can_update_an_existing_classroom()
    {
        // Arrangements
        $classroom = factory(Classroom::class)->create();

        // Actions
        $attributes = [
            'name' => $title = $this->faker->unique()->words(4, true),
            'code' => Str::slug($title),
            'user_id' => factory(User::class)->create()->getKey(),
        ];
        $actual = $this->service->update($classroom->getKey(), $attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $this->assertTrue($actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_can_restore_a_soft_deleted_classroom()
    {
        // Arrangements
        $classroom = factory(Classroom::class)->create();
        $classroom->delete();

        // Actions
        $actual = $this->service->restore($classroom->getKey());
        $restored = $this->service->find($classroom->getKey());

        // Assertions
        $this->assertNull($actual);
        $this->assertNull($restored->deleted_at);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_can_restore_multiple_soft_deleted_classrooms()
    {
        // Arrangements
        $classrooms = factory(Classroom::class, 5)->create();
        $classrooms->each->delete();

        // Actions
        $actual = $this->service->restore($classrooms->pluck('id')->toArray());

        // Assertions
        $this->assertNull($actual);
        $classrooms->each(function ($classroom) {
            $restored = $this->service->find($classroom->getKey());
            $this->assertNull($restored->deleted_at);
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_can_store_a_classroom_to_database()
    {
        // Arrangements
        $attributes = factory(Classroom::class)->make()->toArray();

        // Actions
        $this->service->store($attributes);

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_can_soft_delete_an_existing_classroom()
    {
        // Arrangements
        $classroom = factory(Classroom::class, 3)->create()->random();

        // Actions
        $this->service->destroy($classroom->getKey());
        $classroom = $this->service->withTrashed()->find($classroom->getKey());

        // Assertions
        $this->assertSoftDeleted($this->service->getTable(), $classroom->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_can_soft_delete_multiple_existing_classrooms()
    {
        // Arrangements
        $classrooms = factory(Classroom::class, 3)->create();

        // Actions
        $this->service->destroy($classrooms->pluck('id')->toArray());
        $classrooms = $this->service->withTrashed()->whereIn('id', $classrooms->pluck('id')->toArray())->get();

        // Assertions
        $classrooms->each(function ($classroom) {
            $this->assertSoftDeleted($this->service->getTable(), $classroom->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_classroom()
    {
        // Arrangements
        $classroom = factory(Classroom::class)->create();
        $classroom->delete();

        // Actions
        $this->service->delete($classroom->getKey());

        // Assertions
        $this->assertDatabaseMissing($this->service->getTable(), $classroom->toArray());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_can_permanently_delete_multiple_soft_deleted_classrooms()
    {
        // Arrangements
        $classrooms = factory(Classroom::class, 5)->create();
        $classrooms->each->delete();

        // Actions
        $this->service->delete($classrooms->pluck('id')->toArray());

        // Assertions
        $classrooms->each(function ($classroom) {
            $this->assertDatabaseMissing($this->service->getTable(), $classroom->toArray());
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_should_return_an_array_of_rules()
    {
        // Arrangements
        $rules = $this->service->rules($id = 1);

        // Assertions
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('user_id', $rules);
        $this->assertArrayHasKey('code', $rules);
        $this->assertEquals('required|max:255', $rules['name']);
        $this->assertEquals('required|numeric', $rules['user_id']);
        $this->assertEquals([
            'required', 'regex:/[a-zA-Z0-9\s]+/', Rule::unique($this->service->getTable())->ignore($id)
        ], $rules['code']);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
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
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_can_check_if_user_is_authorized_to_interact_with_classrooms()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin([]));
        $this->withPermissionsPolicy();
        $restricted = factory(Classroom::class, 3)->create()->random();
        $classroom = factory(Classroom::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $restricted = $this->service->authorize($restricted);
        $authorized = $this->service->authorize($classroom);

        // Assertions
        $this->assertFalse($restricted);
        $this->assertTrue($authorized);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:classroom
     * @group  service
     * @group  service:classroom
     * @return void
     */
    public function it_can_check_if_user_has_unrestricted_authorization_to_interact_with_classrooms()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['classrooms.unrestricted']));
        $this->withPermissionsPolicy();
        $classroom = factory(Classroom::class, 3)->create()->random();

        // Actions
        $unrestricted = $this->service->authorize($classroom);

        // Assertions
        $this->assertTrue($unrestricted);
    }
}
