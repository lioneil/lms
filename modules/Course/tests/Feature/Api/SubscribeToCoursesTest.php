<?php

namespace Course\Feature\Api;

use Course\Events\UserSubscribedToCourse;
use Course\Events\UserUnsubscribedToCourse;
use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\User;

/**
 * @package Course\Feature\Api
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SubscribeToCoursesTest extends TestCase
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
     * @group  feature:course
     * @group  courses.subscriptions
     * @return void
     */
    public function a_user_can_view_a_paginated_list_of_subscribed_courses()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.subscriptions', 'courses.owned']), ['courses.subscriptions']);
        $this->withPermissionsPolicy();
        $courses = factory(Course::class, 3)->create();
        $subscriptions = factory(Course::class, 8)->create();
        $subscriptions->each->subscribe($user);

        // Actions
        $response = $this->get(route('api.courses.subscriptions'));

        // Assertions
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [[
                'title', 'subtitle', 'slug',
                'code', 'description', 'icon',
                'image', 'user_id', 'category_id',
                'created', 'modified', 'deleted',
            ]]
        ]);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @return void
     */
    public function a_user_can_subscribe_to_a_published_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.subscribe']), ['courses.subscribe']);
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();
        $course->publish();

        // Actions
        $response = $this->post(route('api.courses.subscribe', $course->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertTrue($course->isSubscribedBy($user));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @return void
     */
    public function a_user_cannot_subscribe_to_an_unpublished_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.unsubscribe']), ['courses.unsubscribe']);
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->post(route('api.courses.unsubscribe', $course->getKey()));

        // Assertions
        $response->assertNotFound();
        $this->assertFalse($course->isSubscribedBy($user));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @return void
     */
    public function a_user_can_unsubscribe_to_a_subscribed_course()
    {
        // Arrangements
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.unsubscribe']), ['courses.unsubscribe']);
        $this->withPermissionsPolicy();
        $course = factory(Course::class, 2)->create()->random();
        $course->publish();
        $course->subscribe($user);

        // Actions
        $response = $this->post(route('api.courses.unsubscribe', $course->getKey()));

        // Assertions
        $response->assertSuccessful();
        $this->assertFalse($course->isSubscribedBy($user));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  service:course
     * @return void
     */
    public function it_should_fire_an_event_when_a_user_subscribes_to_a_course()
    {
        // Arrangements
        Event::fake();
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.subscribe']), ['courses.subscribe']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class, 2)->create()->random();
        $course->publish();

        // Actions
        $response = $this->post(route('api.courses.subscribe', $course->getKey()));

        // Assertions
        Event::assertDispatched(UserSubscribedToCourse::class, function ($e) use ($course) {
            return $e->course->id == $course->id;
        });
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  service:course
     * @return void
     */
    public function it_should_fire_an_event_when_a_user_unsubscribes_from_a_course()
    {
        // Arrangements
        Event::fake();
        Passport::actingAs($user = $this->asNonSuperAdmin(['courses.unsubscribe']), ['courses.unsubscribe']);
        $this->withPermissionsPolicy();

        $course = factory(Course::class, 2)->create()->random();
        $course->publish();
        $course->subscribe($user);

        // Actions
        $response = $this->post(route('api.courses.unsubscribe', $course->getKey()));

        // Assertions
        Event::assertDispatched(UserUnsubscribedToCourse::class, function ($e) use ($course) {
            return $e->course->id == $course->id;
        });
    }
}
