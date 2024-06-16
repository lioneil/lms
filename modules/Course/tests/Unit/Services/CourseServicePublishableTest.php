<?php

namespace Tests\Course\Unit\Services;

use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * @package Course\Unit\Services
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CourseServicePublishableTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
    public function it_can_return_a_paginated_list_of_published_courses()
    {
        // Arrangements
        $courses = factory(Course::class, 10)->create();
        $courses->each->publish();

        // Actions
        $actual = $this->service->listPublished();

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
    public function it_can_publish_a_given_course()
    {
        // Arrangements
        $course = factory(Course::class)->make();

        // Actions
        $actual = $this->service->publish($course);
        $course = $this->service->whereCode($course->code)->first();

        // Assertions
        $this->assertNotNull($course->published_at);
        $this->assertInstanceOf(Course::class, $actual);
        $this->assertDatabaseHas($this->service->getTable(), ['published_at' => $course->published_at]);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_publish_an_existing_course()
    {
        // Arrangements
        $course = factory(Course::class)->create();

        // Actions
        $actual = $this->service->find($course->getKey())->publish();
        $course = $this->service->find($course->getKey());

        // Assertions
        $this->assertNotNull($course->published_at);
        $this->assertInstanceOf(Course::class, $actual);
        $this->assertDatabaseHas($this->service->getTable(), ['published_at' => $course->published_at]);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_unpublish_a_published_course()
    {
        // Arrangements
        $course = factory(Course::class, 3)->create()->random();
        $this->service->find($course->getKey())->publish();

        // Actions
        $actual = $this->service->find($course->getKey())->unpublish();
        $course = $this->service->whereCode($course->code)->first();

        // Assertions
        $this->assertInstanceOf(Course::class, $actual);
        $this->assertNull($course->published_at);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_draft_a_course()
    {
        // Arrangements
        $course = factory(Course::class)->make();

        // Actions
        $actual = $this->service->draft($course);
        $course = $this->service->whereCode($course->code)->first();

        // Assertions
        $this->assertNull($course->published_at);
        $this->assertNotNull($course->drafted_at);
        $this->assertInstanceOf(Course::class, $actual);
        $this->assertDatabaseHas($this->service->getTable(), ['drafted_at' => $course->drafted_at]);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_expire_a_course()
    {
        // Arrangements
        $course = factory(Course::class)->create();

        // Actions
        $actual = $this->service->expire($course);
        $course = $this->service->whereCode($course->code)->first();

        // Assertions
        $this->assertNull($course->drafted_at);
        $this->assertTrue($course->isExpired());
        $this->assertInstanceOf(Course::class, $course);
        $this->assertDatabaseHas($this->service->getTable(), ['expired_at' => $course->expired_at]);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  service
     * @group  service:course
     * @return void
     */
    public function it_can_autoclean_expired_courses()
    {
        // Arrangements
        $courses = factory(Course::class, 3)->create([
            'expired_at' => Carbon::now()->subDays(2)
        ]);
        $courses->each->publish();

        // Actions
        $this->service->autoclean();
        $courses = $this->service->withTrashed()->whereIn(
            'id', $courses->pluck('id')->toArray()
        )->get();

        // Assertions
        $courses->each(function ($course) {
            $this->assertTrue($course->isExpired());
            $this->assertSoftDeleted($course);
        });
    }
}
