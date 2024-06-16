<?php

namespace Tests\Course\Unit\Services;

use Course\Models\Course;
use Course\Observers\CourseObserver;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Event;
use Tests\ActingAsUser;
use Tests\TestCase;
use User\Models\User;

/**
 * @package Course\Unit\Services
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CourseServiceSubscribableTest extends TestCase
{
    use RefreshDatabase, WithFaker, ActingAsUser;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CourseServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_return_a_paginated_list_of_subscribed_courses()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.subscribed']));
        $courses = factory(Course::class, 5)->create();
        $courses->each->subscribe($user);

        // Actions
        $actual = $this->service->listSubscribed();

        // Assertions
        $this->assertInstanceOf(LengthAwarePaginator::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_set_a_course_as_subcribed_by_the_logged_in_user()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.subscribed']));
        $otherUser = factory(User::class)->create();
        $course = factory(Course::class, 2)->create()->random();

        // Actions
        $this->service->find($course->getKey())->subscribe();
        $course = $this->service->find($course->getKey());

        // Assertions
        $this->assertTrue($course->isSubscribedBy($user));
        $this->assertFalse($course->isSubscribedBy($otherUser));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_set_a_course_as_subcribed_by_the_passed_in_user()
    {
        // Arrangements
        $user = $this->asNonSuperAdmin(['courses.subscribed']);
        $otherUser = factory(User::class)->create();
        $course = factory(Course::class, 2)->create()->random();

        // Actions
        $this->service->find($course->getKey())->subscribe($user);
        $course = $this->service->find($course->getKey());

        // Assertions
        $this->assertTrue($course->isSubscribedBy($user));
        $this->assertFalse($course->isSubscribedBy($otherUser));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_set_a_subscribed_course_as_unsubcribed_by_the_logged_in_user()
    {
        // Arrangements
        $this->actingAs($user = $this->asNonSuperAdmin(['courses.subscribed']));
        $course = factory(Course::class, 2)->create()->random();
        $otherUser = factory(User::class)->create();
        $course->subscribe();

        // Actions
        $this->service->find($course->getKey())->unsubscribe();
        $course = $this->service->find($course->getKey());

        // Assertions
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
    public function it_can_set_a_subscribed_course_as_unsubcribed_by_the_passed_in_user()
    {
        // Arrangements
        $user = $this->asNonSuperAdmin(['courses.unsubscribed']);
        $course = factory(Course::class, 2)->create()->random();
        $otherUser = factory(User::class)->create();
        $course->subscribe($user);

        // Actions
        $this->service->find($course->getKey())->unsubscribe($user);
        $course = $this->service->find($course->getKey());

        // Assertions
        $this->assertFalse($course->isSubscribedBy($user));
    }
}
