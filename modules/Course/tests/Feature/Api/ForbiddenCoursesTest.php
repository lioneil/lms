<?php

namespace Course\Feature\Api;

use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ForbiddenCoursesTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CourseServiceInterface::class);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @group  feature:api:course:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_retrieve_the_paginated_list_of_all_courses()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();

        // Actions
        $response = $this->post(route('api.courses.index'));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @group  feature:api:course:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_store_a_course_to_database()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Course::class)->make()->toArray();
        $response = $this->post(route('api.courses.store'), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @group  feature:api:course:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_retrieve_a_single_course()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 2)->create()->random();

        // Actions
        $response = $this->get(route('api.courses.show', $course->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @group  feature:api:course:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_update_a_course()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 2)->create()->random();

        // Actions
        $attributes = factory(Course::class)->make()->toArray();
        $response = $this->put(route('api.courses.update', $course->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @group  feature:api:course:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_soft_delete_a_course()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.courses.destroy', $course->getKey()));
        $course = $this->service->withTrashed()->find($course->getKey());

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @group  feature:api:course:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_soft_delete_multiple_courses()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $courses = factory(Course::class, 3)->create();

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->delete(route('api.courses.destroy', 'null'), $attributes);
        $courses = $this->service->onlyTrashed();

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @group  feature:api:course:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_retrieve_the_paginated_list_of_all_trashed_courses()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $courses = factory(Course::class, 2)->create();
        $courses->each->delete();

        // Actions
        $response = $this->get(route('api.courses.trashed'));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @group  feature:api:course:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_restore_destroyed_courses()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();
        $course->delete();

        // Actions
        $response = $this->patch(route('api.courses.restore', $course->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @group  feature:api:course:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_restore_multiple_destroyed_courses()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $courses = factory(Course::class, 3)->create();
        $courses->each->delete();

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->patch(route('api.courses.restore'), $attributes);

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @group  feature:api:course:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_permanently_delete_a_course()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 2)->create()->random();

        // Actions
        $response = $this->delete(route('api.courses.delete', $course->getKey()));

        // Assertions
        $response->assertForbidden();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @group  feature:api:course:forbidden
     * @return void
     */
    public function an_unpermitted_user_cannot_permanently_delete_multiple_courses()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['wrong-permission']), ['wrong-permission']);
        $this->withPermissionsPolicy();
        $courses = factory(Course::class, 3)->create();

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->delete(route('api.courses.delete'), $attributes);

        // Assertions
        $response->assertForbidden();
    }

}
