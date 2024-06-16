<?php

namespace Course\Feature\Admin;

use Course\Events\UserSubscribedToCourse;
use Course\Events\UserUnsubscribedToCourse;
use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\User;

/**
 * @package Course\Feature\Admin
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
    public function a_super_user_can_view_a_paginated_list_of_owned_subscribed_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $courses = factory(Course::class, 3)->create();
        $subscriptions = factory(Course::class, 8)->create();
        $subscriptions->each->subscribe($user);

        // Actions
        $response = $this->get(route('courses.subscriptions'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('course::public.subscriptions')
                 ->assertSeeText(trans('Subscribed Courses'))
                 ->assertSeeTextInOrder($subscriptions->pluck('title')->toArray())
                 ->assertDontSeeText($courses->random()->title);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.subscribe
     * @return void
     */
    public function a_super_user_can_subscribe_to_a_published_course()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $course = factory(Course::class, 3)->create()->random();
        $course->publish();

        // Actions
        $response = $this->post(route('courses.subscribe', $course->getKey()));

        // Assertions
        $response->assertRedirect(route('courses.show', $course->getKey()));
        $this->assertTrue($course->isSubscribedBy($user));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.subscribe
     * @return void
     */
    public function a_super_user_cannot_subscribe_to_an_unpublished_course()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->post(route('courses.subscribe', $course->getKey()));

        // Assertions
        $response->assertNotFound();
        $this->assertFalse($course->isSubscribedBy($user));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.unsubscribe
     * @return void
     */
    public function a_super_user_can_unsubscribe_to_a_subscribed_course()
    {
        // Arrangements
        $this->actingAs($user = $this->superAdmin);
        $course = factory(Course::class, 3)->create()->random();
        $course->publish();
        $course->subscribe($user);

        // Actions
        $response = $this->post(
            route('courses.unsubscribe', $course->getKey()), [], ['HTTP_REFERER' => route('courses.index')]
        );

        // Assertions
        $response->assertRedirect(route('courses.index'));
        $this->assertFalse($course->isSubscribedBy($user));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.subscriptions
     * @return void
     */
    public function a_logged_in_user_can_view_a_paginated_list_of_owned_subscribed_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.subscriptions']));
        $this->withPermissionsPolicy();

        $courses = factory(Course::class, 3)->create();
        $subscriptions = factory(Course::class, 8)->create();
        $subscriptions->each->subscribe($user);

        // Actions
        $response = $this->get(route('courses.subscriptions'));

        // Assertions
        $response->assertSuccessful()
                 ->assertViewHas('resources')
                 ->assertViewIs('course::public.subscriptions')
                 ->assertSeeText(trans('Subscribed Courses'))
                 ->assertSeeTextInOrder($subscriptions->pluck('title')->toArray())
                 ->assertDontSeeText($courses->random()->title);
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.subscriptions
     * @return void
     */
    public function a_logged_out_user_cannot_view_a_paginated_list_of_owned_subscribed_courses()
    {
        // Arrangements
        $user = $this->asNonSuperAdmin(['courses.subscriptions']);
        $this->withPermissionsPolicy();

        $courses = factory(Course::class, 3)->create();
        $subscriptions = factory(Course::class, 8)->create();
        $subscriptions->each->subscribe($user);

        // Actions
        $response = $this->get(route('courses.subscriptions'));

        // Assertions
        $response->assertRedirect(route('login'));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.subscriptions
     * @return void
     */
    public function a_user_can_subscribe_to_a_published_course()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.show', 'courses.subscribe']));
        $course = factory(Course::class, 3)->create()->random();
        $course->publish();

        // Actions
        $response = $this->post(route('courses.subscribe', $course->getKey()));

        // Assertions
        $response->assertRedirect(route('courses.show', $course->getKey()));
        $this->assertTrue($course->isSubscribedBy($user));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.subscribe
     * @return void
     */
    public function a_user_cannot_subscribe_to_an_unpublished_course()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.show', 'courses.subscribe']));
        $course = factory(Course::class, 3)->create()->random();

        // Actions
        $response = $this->post(route('courses.subscribe', $course->getKey()));

        // Assertions
        $response->assertNotFound();
        $this->assertFalse($course->isSubscribedBy($user));
    }

    /**
     * @test
     * @group  feature
     * @group  feature:course
     * @group  courses.subscribe
     * @return void
     */
    public function a_user_can_unsubscribe_from_a_subscribed_course()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.unsubscribe']));
        $course = factory(Course::class, 3)->create()->random();
        $course->publish();
        $course->subscribe($user);

        // Actions
        $response = $this->post(
            route('courses.unsubscribe', $course->getKey()), [], ['HTTP_REFERER' => route('courses.index')]
        );

        // Assertions
        $response->assertRedirect(route('courses.index'));
        $this->assertFalse($course->isSubscribedBy($user));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_should_fire_an_event_when_a_user_subscribes_to_a_course()
    {
        // Arrangements
        Event::fake();

        $course = factory(Course::class, 2)->create()->random();
        $user = factory(User::class)->create();

        // Actions
        $course->subscribe($user);

        // Assertions
        Event::assertDispatched(UserSubscribedToCourse::class, function ($e) use ($course) {
            return $e->course->id == $course->id;
        });
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_should_fire_an_event_when_a_user_unsubscribes_from_a_course()
    {
        // Arrangements
        Event::fake();

        $course = factory(Course::class, 2)->create()->random();
        $user = factory(User::class)->create();
        $course->subscribe($user);

        // Actions
        $course->unsubscribe($user);

        // Assertions
        Event::assertDispatched(UserUnsubscribedToCourse::class, function ($e) use ($course) {
            return $e->course->id == $course->id;
        });
    }
}
