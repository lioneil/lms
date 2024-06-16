<?php

namespace Course\Unit\Models;

use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @package Course\Unit\Models
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CoursePublishableTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase, WithFaker;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(CourseServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_checks_if_course_has_been_published()
    {
        // Arrangements
        $course = factory(Course::class)->create();

        // Actions
        $actual = $course->publish();

        // Assertions
        $this->assertTrue($actual->isPublished());
        $this->assertFalse($actual->isDrafted());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_checks_if_course_is_unpublished()
    {
        // Arrangements
        $actual = factory(Course::class)->create();

        // Assertions
        $this->assertTrue($actual->isUnpublished());
        $this->assertFalse($actual->isPublished());
        $this->assertFalse($actual->isDrafted());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_checks_if_course_has_been_drafted()
    {
        // Arrangements
        $course = factory(Course::class)->create();

        // Actions
        $actual = $course->draft();

        // Assertions
        $this->assertTrue($actual->isDrafted());
        $this->assertFalse($actual->isPublished());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_checks_if_course_has_expired()
    {
        // Arrangements
        $course = factory(Course::class)->create();

        // Actions
        $actual = $course->expire();

        // Assertions
        $this->assertTrue($actual->isExpired());
        $this->assertFalse($actual->isDrafted());
    }
}
