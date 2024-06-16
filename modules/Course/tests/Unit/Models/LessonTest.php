<?php

namespace Course\Unit\Models;

use Course\Models\Course;
use Course\Models\Lesson;
use Course\Services\LessonServiceInterface;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;

/**
 * @package Course\Unit\Models
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class LessonTest extends TestCase
{
    use ActingAsUser, DatabaseMigrations, RefreshDatabase, WithFaker, WithPermissionsPolicy;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(LessonServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_can_check_if_lesson_is_first_lesson_of_course()
    {
        // Arrangements
        $course = factory(Course::class)->create();
        $lessons = factory(Lesson::class, 5)->create([
            'course_id' => $course->getKey(),
        ])->each(function ($lesson, $i) {
            $lesson->sort = $i+1;
            $lesson->save();
        });

        // Actions
        $actual = $course->lessons->random()->isFirst();

        // Assertions
        $this->assertIsBool($actual);
        $this->assertTrue($lessons->first()->isFirst());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_can_check_if_lesson_is_last_lesson_of_course()
    {
        // Arrangements
        $course = factory(Course::class)->create();
        $lessons = factory(Lesson::class, 5)->create([
            'course_id' => $course->getKey()
        ])->each(function ($lesson, $i) {
            $lesson->sort = $i+1;
            $lesson->save();
        });

        // Actions
        $actual = $course->lessons->random()->isLast();

        // Assertions
        $this->assertIsBool($actual);
        $this->assertTrue($lessons->last()->isLast());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_has_an_accessor_called_icon()
    {
        // Arrangements
        $lesson = factory(Lesson::class)->create();

        // Actions
        $actual = $lesson->icon;

        // Assertions
        $this->assertTrue($lesson->hasGetMutator('icon'));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_has_an_accessor_called_interactive()
    {
        // Arrangements
        $lesson = factory(Lesson::class)->create();

        // Actions
        $actual = $lesson->interactive;

        // Assertions
        $this->assertTrue($lesson->hasGetMutator('interactive'));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_has_an_accessor_called_scorm()
    {
        // Arrangements
        $lesson = factory(Lesson::class)->create();

        // Actions
        $actual = $lesson->scorm;

        // Assertions
        $this->assertTrue($lesson->hasGetMutator('scorm'));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_has_an_accessor_called_imsmanifest()
    {
        // Arrangements
        $lesson = factory(Lesson::class)->create();

        // Actions
        $actual = $lesson->imsmanifest;

        // Assertions
        $this->assertTrue($lesson->hasGetMutator('imsmanifest'));
    }
}
