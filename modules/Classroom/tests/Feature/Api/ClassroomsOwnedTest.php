<?php

namespace Classroom\Feature\Api;

use Classroom\Models\Classroom;
use Classroom\Services\ClassroomServiceInterface;
use Course\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithForeignKeys;
use Tests\WithPermissionsPolicy;

/**
 * @package Classroom\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ClassroomsOwnedTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy, WithForeignKeys;

     /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ClassroomServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_can_only_retrieve_their_owned_paginated_list_of_classrooms()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['classrooms.index']), ['classrooms.index']);
        $this->withPermissionsPolicy();

        $classroom = factory(Classroom::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $response = $this->get(route('api.classrooms.index'));

        // Assertions
        $response
            ->assertSuccessful()
            ->assertJson(['data' => [[
                'user_id' => $user->getKey(),
            ]]])
            ->assertJsonStructure([
                'data' => [[
                    'name', 'code', 'description',
                    'user_id',
                ]],
            ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_trashed_classrooms()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['classrooms.trashed', 'classrooms.owned']), ['classrooms.trashed']);
        $this->withPermissionsPolicy();
        $classrooms = factory(Classroom::class, 3)->create(['user_id' => $user->getKey()]);
        $classrooms->each->delete();

        // Actions
        $response = $this->get(route('api.classrooms.trashed'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [[
                    'user_id' => $user->getKey(),
                ]]])
                 ->assertJsonStructure([
                    'data' => [[
                        'user_id',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classrooms
     * @return void
     */
    public function a_user_can_visit_owned_classroom_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['classrooms.show', 'classrooms.owned',]), ['classrooms.show']);
        $this->withPermissionsPolicy();

        $classroom = factory(Classroom::class, 3)->create(['user_id' => $user->getKey()])->random();

                // Actions
        $response = $this->get(route('api.classrooms.show', $classroom->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'user_id' => $user->getKey(),
                ]])
                 ->assertJsonStructure([
                    'data' => [
                        'user_id',
                    ],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_can_visit_any_classroom_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['classrooms.show']), ['classrooms.show']);
        $this->withPermissionsPolicy();

        $classroom = factory(Classroom::class, 2)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->get(route('api.classrooms.show', $classroom->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'user_id' => $user->getKey(),
                ]])
                 ->assertJsonStructure([
                    'data' => [
                        'user_id',
                    ],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classrooms
     * @return void
     */
    public function a_user_can_store_a_classroom_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['classrooms.store']), ['classrooms.store']);
        $this->withPermissionsPolicy();

        $classroom = factory(Classroom::class)->create();

        // Actions
        $classroom = factory(Classroom::class)->make(['user_id' => $user->getKey()]);
        $attributes = array_merge($classroom->toArray(), [
            'courses' => factory(Course::class)->create()->pluck('id')->toArray(),
        ]);
        $response = $this->post(route('api.classrooms.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('courses')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_can_only_update_their_owned_classrooms()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['classrooms.owned', 'classrooms.update']), ['classrooms.update']);
        $this->withPermissionsPolicy();

        $classrooms = factory(classroom::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $classroom = factory(Classroom::class)->make();
        $attributes = array_merge($classroom->toArray(), [
            'courses' => factory(Course::class)->create()->pluck('id')->toArray(),
        ]);
        $response = $this->put(route('api.classrooms.update', $classrooms->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), collect($attributes)->except('courses')->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_cannot_update_classrooms_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['classrooms.owned', 'classrooms.update']), ['classrooms.update']);
        $this->withPermissionsPolicy();
        $classroom = factory(Classroom::class, 3)->create()->random();

        // Actions
        $attributes = factory(classroom::class)->make()->toArray();
        $response = $this->put(route('api.classrooms.update', $classroom->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($classroom->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_can_only_restore_owned_content()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['classrooms.restore', 'classrooms.owned']), ['classrooms.restore']);
        $this->withPermissionsPolicy();
        $classroom = factory(Classroom::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->patch(route('api.classrooms.restore', $classroom->getKey()));
        $classroom = $this->service->find($classroom->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($classroom->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_can_only_multiple_restore_owned_classrooms()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['classrooms.restore', 'classrooms.owned']), ['classrooms.restore']);
        $this->withPermissionsPolicy();
        $classrooms = factory(Classroom::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['id' => $classrooms->pluck('id')->toArray()];
        $response = $this->patch(route('api.classrooms.restore'), $attributes);
        $classrooms = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $classrooms->each(function ($classroom) {
            $this->assertFalse($classroom->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_cannot_restore_classroom_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['classrooms.restore', 'classrooms.owned']), ['classrooms.restore']);
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['classrooms.owned', 'classrooms.restore']);
        $classroom = factory(Classroom::class, 3)->create(['user_id' => $otherUser->getKey()])->random();

        // Actions
        $response = $this->patch(route('api.classrooms.restore', $classroom->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($classroom->getTable(), $classroom->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_cannot_multiple_restore_classrooms_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['classrooms.restore', 'classrooms.owned']), ['classrooms.restore']);
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['classrooms.owned', 'classrooms.restore']);
        $classrooms = factory(Classroom::class, 3)->create(['user_id' => $otherUser->getKey()]);

        // Actions
        $attributes = ['id' => $classrooms->pluck('id')->toArray()];
        $response = $this->patch(route('api.classrooms.restore'), $attributes);

        // Assertions
        $response->assertForbidden();
        $classrooms->each(function ($classroom) {
            $this->assertDatabaseHas($classroom->getTable(), $classroom->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_classroom()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['classrooms.destroy', 'classrooms.owned']), ['classrooms.destroy']);
        $this->withPermissionsPolicy();
        $classroom = factory(Classroom::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.classrooms.destroy', $classroom->getKey()));
        $classroom = $this->service->withTrashed()->find($classroom->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($classroom->getTable(), $classroom->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_classrooms()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['classrooms.destroy', 'classrooms.owned']), ['classrooms.destroy']);
        $this->withPermissionsPolicy();
        $classrooms = factory(Classroom::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['id' => $classrooms->pluck('id')->toArray()];
        $response = $this->delete(route('api.classrooms.destroy', 'null'), $attributes);
        $classrooms = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $classrooms->each(function ($classroom) {
            $this->assertSoftDeleted($classroom->getTable(), $classroom->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_cannot_soft_delete_classroom_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['classrooms.destroy', 'classrooms.owned']), ['classrooms.destroy']);
        $this->withPermissionsPolicy();
        $classroom = factory(Classroom::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.classrooms.destroy', $classroom->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($classroom->getTable(), $classroom->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_cannot_multiple_soft_delete_classrooms_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['classrooms.destroy', 'classrooms.owned']), ['classrooms.destroy']);
        $this->withPermissionsPolicy();
        $classrooms = factory(Classroom::class, 3)->create();

        // Actions
        $attributes = ['id' => $classrooms->pluck('id')->toArray()];
        $response = $this->delete(route('api.classrooms.destroy', 'null'), $attributes);

        // Assertions
        $response->assertForbidden();
        $classrooms->each(function ($classroom) {
            $this->assertDatabaseHas($classroom->getTable(), $classroom->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_classroom()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['classrooms.delete', 'classrooms.owned']), ['classrooms.delete']);
        $this->withPermissionsPolicy();
        $classroom = factory(Classroom::class, 2)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.classrooms.delete', $classroom->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseMissing($classroom->getTable(), $classroom->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_classrooms()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['classrooms.delete', 'classrooms.owned']), ['classrooms.delete']);
        $this->withPermissionsPolicy();
        $classrooms = factory(Classroom::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['id' => $classrooms->pluck('id')->toArray()];
        $response = $this->delete(route('api.classrooms.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $classrooms->each(function ($classroom) {
            $this->assertDatabaseMissing($classroom->getTable(), $classroom->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_cannot_permanently_delete_classroom_owned_by_other_classrooms()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['classrooms.delete', 'classrooms.owned']), ['classrooms.delete']);
        $this->withPermissionsPolicy();
        $classroom = factory(Classroom::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.classrooms.delete', $classroom->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($classroom->getTable(), $classroom->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:classroom
     * @return void
     */
    public function a_user_cannot_multiple_permanently_delete_classrooms_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['classrooms.delete', 'classrooms.owned']), ['classrooms.delete']);
        $this->withPermissionsPolicy();
        $classrooms = factory(Classroom::class, 3)->create();

        // Actions
        $attributes = ['id' => $classrooms->pluck('id')->toArray()];
        $response = $this->delete(route('api.classrooms.delete'), $attributes);

        // Assertions
        $response->assertForbidden();
        $classrooms->each(function ($classroom) {
            $this->assertDatabaseHas($classroom->getTable(), $classroom->toArray());
        });
    }
}
