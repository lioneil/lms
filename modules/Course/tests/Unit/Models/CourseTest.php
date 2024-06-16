<?php

namespace Course\Unit\Models;

use Course\Enumerations\LessonMetadataKeys;
use Course\Events\UserSubscribedToCourse;
use Course\Events\UserUnsubscribedToCourse;
use Course\Models\Course;
use Course\Models\Lesson;
use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\ActingAsUser;
use Tests\TestCase;
use Tests\WithPermissionsPolicy;
use User\Models\User;

/**
 * @package Course\Unit\Models
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class CourseTest extends TestCase
{
    use ActingAsUser, DatabaseMigrations, RefreshDatabase, WithFaker, WithPermissionsPolicy;

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
    public function it_returns_the_first_lesson()
    {
        // Arrangements
        $course = factory(Course::class)->create();
        $lessons = factory(Lesson::class, 2)->create([
            'course_id' => $course->getKey(),
            'type' => LessonMetadataKeys::SECTION_KEY,
        ])->each(function ($lesson, $i) use ($course) {
            $content = factory(Lesson::class)->create([
                'course_id' => $course->getKey(),
                'type' => LessonMetadataKeys::VIDEO_KEY,
            ]);
            $lesson->addChild($content);
        });

        // Actions
        $actual = $course->getFirstLesson();

        // Assertions
        $this->assertInstanceOf(Lesson::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_returns_the_last_lesson()
    {
        // Arrangements
        $course = factory(Course::class)->create();
        $lessons = factory(Lesson::class, 2)->create([
            'course_id' => $course->getKey(),
            'type' => LessonMetadataKeys::SECTION_KEY,
        ])->each(function ($lesson, $i) use ($course) {
            $content = factory(Lesson::class)->create([
                'course_id' => $course->getKey(),
                'type' => LessonMetadataKeys::VIDEO_KEY,
            ]);
            $lesson->addChild($content);
        });

        // Actions
        $actual = $course->getLastLesson();

        // Assertions
        $this->assertInstanceOf(Lesson::class, $actual);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_has_an_accessor_called_playlist()
    {
        // Arrangements
        $course = factory(Course::class)->create();

        // Actions
        $actual = $course->playlist;

        // Assertions
        $this->assertTrue($course->hasGetMutator('playlist'));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_has_an_accessor_called_playables()
    {
        // Arrangements
        $course = factory(Course::class)->create();

        // Actions
        $actual = $course->playables;

        // Assertions
        $this->assertTrue($course->hasGetMutator('playables'));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_has_an_accessor_called_meta()
    {
        // Arrangements
        $course = factory(Course::class)->create();
        $lessons = factory(Lesson::class, 2)->create([
            'course_id' => $course->getKey()
        ])->each(function ($lesson, $i) {
            $content = factory(Lesson::class)->create();
            $lesson->addChild($content);
        });

        // Actions
        $actual = $course->meta;

        // Assertions
        $this->assertTrue($course->hasGetMutator('meta'));
        $this->assertSame(2, $actual['lessons']['count']);
    }

    /**
     * @test
     * @group  unit
     * @group  unit:course
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

    /**
     * @test
     * @group  unit
     * @group  unit:service
     * @group  unit:service:course
     * @return void
     */
    public function it_can_retrieve_the_next_lesson()
    {
        // Arrangements
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $lessons = factory(Lesson::class, 2)->create([
            'course_id' => $course->getKey(),
            'type' => LessonMetadataKeys::SECTION_KEY,
        ])->each(function ($lesson, $i) use ($course) {
            $lesson->attachToSelf();
            $content = factory(Lesson::class)->create([
                'course_id' => $course->getKey(),
                'type' => LessonMetadataKeys::VIDEO_KEY,
            ]);
            $lesson->addChild($content);
        });

        $course->makeLockable();
        $course->publish();
        $course->subscribe($user);

        $course->getFirstLesson()->markAsComplete($user);

        // Actions
        $actual = $course->getNextLesson($course->getFirstLesson(), $user);

        // Assertions
        $this->assertInstanceOf(Lesson::class, $actual);
    }
}
