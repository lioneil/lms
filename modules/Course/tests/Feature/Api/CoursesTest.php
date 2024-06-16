<?php

namespace Course\Feature\Api;

use Course\Models\Course;
use Course\Models\Lesson;
use Course\Models\Tag;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Taxonomy\Models\Category;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CoursesTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CourseServiceInterface::class);
        $this->superAdmin = $this->asSuperAdmin();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_courses()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.index', 'courses.owned']), ['courses.index']);
        $this->withPermissionsPolicy();

        $courses = factory(Course::class, 2)->create(['user_id' => $user->getKey()]);

        // Actions
        $response = $this->get(route('api.courses.index'));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [[
                    'user_id' => $user->getKey(),
                ]]])
                 ->assertJsonStructure([
                    'data' => [[
                        'title', 'subtitle', 'slug',
                        'code', 'description', 'icon',
                        'image', 'user_id', 'category_id',
                        'created', 'modified', 'deleted',
                    ]],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_can_only_view_their_owned_paginated_list_of_trashed_courses($value='')
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.trashed', 'courses.owned']), ['courses.trashed']);
        $this->withPermissionsPolicy();

        $courses = factory(Course::class, 2)->create([
            'user_id' => $user->getKey(),
        ]);
        $courses->each->delete();

        // Actions
        $response = $this->get(route('api.courses.trashed'));

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
     * @group  feature:api:course
     * @return void
     */
    public function a_user_can_visit_owned_course_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.show', 'courses.owned']), ['courses.show']);
        $this->withPermissionsPolicy();

        $category = factory(Category::class)->create();
        $tags = factory(Tag::class, 3)->create();
        $course = factory(Course::class, 3)->create([
            'category_id' => $category->getKey(),
            'user_id' => $user->getKey(),
        ])->random();
        $course->tags()->sync($tags->pluck('id')->toArray());
        $lessons = factory(Lesson::class, 5)->create([
            'title' => sprintf('Lesson: %s', title_case($this->faker->words(4, $asText = true))),
            'course_id' => $course->getKey(),
        ]);

        // Actions
        $response = $this->get(route('api.courses.show', $course->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'user_id' => $user->getKey(),
                    'category_id' => $category->getKey(),
                 ]])
                 ->assertJsonStructure([
                    'data' => [
                        'category_id',
                        'user_id',
                    ],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_can_visit_any_course_page()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.show']), ['courses.show']);
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin([
            'courses.edit', 'courses.show', 'courses.owned', 'courses.publish', 'courses.destroy'
        ]);

        $category = factory(Category::class)->create();
        $tags = factory(Tag::class, 3)->create();
        $course = factory(Course::class, 3)->create([
            'category_id' => $category->getKey(),
            'user_id' => $otherUser->getKey(),
        ])->random();
        $course->tags()->sync($tags->pluck('id')->toArray());
        $lessons = factory(Lesson::class, 10)->create([
            'title' => sprintf('Lesson: %s', ucwords($this->faker->words(4, $asText = true))),
            'course_id' => $course->getKey(),
        ]);

        // Actions
        $response = $this->get(route('api.courses.show', $course->getKey()));

        // Assertions
        $response->assertSuccessful()
                 ->assertJson(['data' => [
                    'user_id' => $otherUser->getKey(),
                    'category_id' => $category->getKey(),
                 ]])
                 ->assertJsonStructure([
                    'data' => [
                        'category_id',
                        'user_id',
                    ],
                ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_can_store_a_course_to_database()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.store']), ['courses.store']);
        $this->withPermissionsPolicy();

        // Actions
        $attributes = factory(Course::class)->make(['user_id' => $user->getKey()])->toArray();
        $response = $this->post(route('api.courses.store'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
    }


    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_can_only_update_their_owned_courses()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.owned', 'courses.update']), ['courses.update']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = factory(Course::class)->make()->toArray();
        $response = $this->put(route('api.courses.update', $course->getKey()), $attributes);

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseHas($course->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_cannot_update_others_courses()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.owned', 'courses.update']), ['courses.update']);
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $attributes = factory(Course::class)->make()->toArray();
        $response = $this->put(route('api.courses.update', $course->getKey()), $attributes);

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseMissing($course->getTable(), $attributes);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_can_only_restore_owned_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.restore', 'courses.owned']), ['courses.restore']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->patch(route('api.courses.restore', $course->getKey()));
        $course = $this->service->find($course->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($course->trashed());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_can_only_restore_owned_multiple_courses()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.restore', 'courses.owned']), ['courses.restore']);
        $this->withPermissionsPolicy();

        $courses = factory(Course::class, 3)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->patch(route('api.courses.restore'), $attributes);
        $courses = $this->service->whereIn('id', $attributes['id'])->get();

        // Assertions
        $response->assertSuccessful();
        $courses->each(function ($course) {
            $this->assertFalse($course->trashed());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_cannot_restore_course_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.restore', 'courses.owned']), ['courses.restore']);
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['courses.owned', 'courses.restore']);
        $course = factory(Course::class, 3)->create(['user_id' => $otherUser->getKey()])->random();

        // Actions
        $response = $this->patch(route('api.courses.restore', $course->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($course->getTable(), $course->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_cannot_multiple_restore_courses_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.restore', 'courses.owned']), ['courses.restore']);
        $this->withPermissionsPolicy();

        $otherUser = $this->asNonSuperAdmin(['courses.owned', 'courses.restore']);
        $courses = factory(Course::class, 3)->create(['user_id' => $otherUser->getKey()]);

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->patch(route('api.courses.restore'), $attributes);

        // Assertions
        $response->assertForbidden();
        $courses->each(function ($course) {
            $this->assertDatabaseHas($course->getTable(), $course->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_can_only_soft_delete_owned_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.destroy', 'courses.owned']), ['courses.destroy']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.courses.destroy', $course->getKey()));
        $course = $this->service->withTrashed()->find($course->getKey());

        // Assertions
        $response->assertSuccessful();
        $this->assertSoftDeleted($course->getTable(), $course->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_can_only_multiple_soft_delete_owned_courses()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.destroy', 'courses.owned']), ['courses.destroy']);
        $this->withPermissionsPolicy();

        $courses = factory(Course::class, 2)->create(['user_id' => $user->getKey()]);

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->delete(route('api.courses.destroy', 'null'), $attributes);
        $courses = $this->service->onlyTrashed();

        // Assertions
        $response->assertSuccessful();
        $courses->each(function ($course) {
            $this->assertSoftDeleted($course->getTable(), $course->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_cannot_soft_delete_course_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.destroy', 'courses.owned']), ['courses.destroy']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.courses.destroy', $course->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($course->getTable(), $course->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_cannot_multiple_soft_delete_courses_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.destroy', 'courses.owned']), ['courses.destroy']);
        $this->withPermissionsPolicy();
        $courses = factory(Course::class, 3)->create();

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->delete(route('api.courses.destroy', 'null'), $attributes);

        // Assertions
        $response->assertForbidden();
        $courses->each(function ($course) {
            $this->assertDatabaseHas($course->getTable(), $course->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_can_only_permanently_delete_owned_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.delete', 'courses.owned']), ['courses.delete']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $response = $this->delete(route('api.courses.delete', $course->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertDatabaseMissing($course->getTable(), $course->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_can_only_multiple_permanently_delete_owned_courses()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.delete', 'courses.owned']), ['courses.delete']);
        $this->withPermissionsPolicy();

        $courses = factory(Course::class, 3)->create(['user_id' => $user->getKey()])->random();

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->delete(route('api.courses.delete'), $attributes);

        // Assertions
        $response->assertSuccessful();
        $courses->each(function ($course) {
            $this->assertDatabaseMissing($course->getTable(), $course->toArray());
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_cannot_permanently_delete_course_owned_by_other_courses()
    {
        // Arrangements
        Passport::actingAs($this->asNonSuperAdmin(['courses.delete', 'courses.owned']), ['courses.delete']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->delete(route('api.courses.delete', $course->getKey()));

        // Assertions
        $response->assertForbidden();
        $this->assertDatabaseHas($course->getTable(), $course->toArray());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_user_cannot_multiple_permanently_delete_courses_owned_by_other_users()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.delete', 'courses.owned']), ['courses.delete']);
        $this->withPermissionsPolicy();

        $courses = factory(Course::class, 3)->create()->random();

        // Actions
        $attributes = ['id' => $courses->pluck('id')->toArray()];
        $response = $this->delete(route('api.courses.delete'), $attributes);

        // Assertions
        $response->assertForbidden();
        $courses->each(function ($course) {
            $this->assertDatabaseHas($course->getTable(), $course->toArray());
        });
    }
}
