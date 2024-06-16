<?php

namespace Course\Unit\Listeners;

use Course\Enumerations\CourseDictionary;
use Course\Enumerations\LessonMetadataKeys;
use Course\Listeners\UnlockNextLessonForUser;
use Course\Models\Course;
use Course\Models\Lesson;
use Course\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Subscription\Services\ProgressionServiceInterface;
use Tests\TestCase;

/**
 * @package Course\Unit\Listeners
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class UnlockNextLessonForUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group  unit
     * @group  unit:listeners
     * @group  unit:listeners:course
     * @return void
     */
    public function it_triggers_the_listener_when_the_user_progressed_event_is_fired()
    {
        // Arrangement
        $listener = Mockery::spy(UnlockNextLessonForUser::class);
        $this->app->instance(UnlockNextLessonForUser::class, $listener);

        $user = factory(Student::class)->create();
        $course = factory(Course::class)->create();
        $lessons = factory(Lesson::class, 4)->create([
            'course_id' => $course->getKey(),
        ])->each->attachToSelf();

        $course->publish();
        $course->subscribe($user);

        // Actions
        $course->lessons->first()->markAsComplete($user);
        $progress = $course->progressionsOf($user)->first();

        // Assertions
        $listener->shouldHaveReceived('handle')->with(Mockery::on(function ($event) use ($progress) {
            return $event->progression->id == $progress->id;
        }));
    }

    /**
     * @test
     * @group  unit
     * @group  unit:listeners
     * @group  unit:listeners:course
     * @return void
     */
    public function it_unlocks_the_next_lesson_of_a_course_for_student()
    {
        // Arrangements
        $user = factory(Student::class)->create();
        $course = factory(Course::class)->create();
        $lessons = factory(Lesson::class, 4)->create([
            'sort' => 0,
            'type' => LessonMetadataKeys::VIDEO_KEY,
            'course_id' => $course->getKey(),
        ])->each->attachToSelf();

        $course->publish();
        $course->subscribe($user);

        // Actions
        $course->lessons->get(0)->markAsComplete($user);
        $progress = $course->progressionsOf($user)->first();

        $expected = [
            'id' => $lessons->get(1)->getKey(),
            'locked' => false,
            'status' => CourseDictionary::IN_PROGRESS,
            'type' => LessonMetadataKeys::VIDEO_KEY,
        ];

        $actual = collect($progress->metadata['lessons'])->where(
            'id', $lessons->get(1)->getKey()
        )->first();

        // Assertions
        $this->assertSame($expected, $actual);
    }
}
