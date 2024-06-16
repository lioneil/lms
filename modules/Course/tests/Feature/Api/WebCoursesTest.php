<?php

namespace Course\Feature\Api;

use Course\Models\Course;
use Course\Models\Lesson;
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
class WebCoursesTest extends TestCase
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
     * @return void
     */
    public function a_user_can_only_view_the_paginated_list_of_published_courses()
    {
        // Arrangements
        $unpublished = factory(Course::class)->create();
        $courses = factory(Course::class, 4)->create();
        $courses->each->publish();

        // Actions
        $response = $this->get(route('api.courses.all'));

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse(in_array(
            $unpublished->getKey(),
            $response->original->pluck('id')->toArray()
        ));
        $response->assertJsonStructure([
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
    public function a_user_can_visit_any_published_course_page()
    {
        // Arrangements
        $course = factory(Course::class, 4)->create()->random();
        $course->publish();

        // Actions
        $response = $this->get(route('api.courses.single', $course->getSlug()));

        // Assertions
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                'title', 'subtitle', 'slug',
                'code', 'description', 'icon',
                'image', 'user_id', 'category_id',
                'created', 'modified', 'deleted',
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
    public function a_user_cannot_visit_any_unpublished_course_page()
    {
        // Arrangements
        $course = factory(Course::class)->create();

        // Actions
        $response = $this->get(route('api.courses.single', $course->getSlug()));

        // Assertions
        $response->assertNotFound();
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function a_subscribed_user_can_visit_any_lockable_published_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.single']));
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        factory(Lesson::class, 3)->create([
            'course_id' => $course->getKey()
        ]);

        $course->makeLockable();
        $course->publish();
        $course->subscribe($user);

        // Actions
        $response = $this->get(route('api.courses.single', $course->getSlug()));

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($course->lessons->first()->isLocked());
        $this->assertFalse($course->lessons->get(1)->isUnlocked());
    }

    /**
     * @test
     * @group  feature
     * @group  feature:api
     * @group  feature:api:course
     * @return void
     */
    public function an_unsubscribed_user_can_visit_any_lockable_published_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.single']));
        $this->withPermissionsPolicy();

        $course = factory(Course::class)->create();
        factory(Lesson::class, 3)->create([
            'course_id' => $course->getKey()
        ]);

        $course->makeLockable();
        $course->publish();

        // Actions
        $response = $this->get(route('api.courses.single', $course->getSlug()));

        // Assertions
        $response->assertSuccessful();
        $this->assertTrue($course->lessons->random()->isLocked());
    }
}
