<?php

namespace Course\Unit\Events;

use Course\Models\Course;
use Course\Models\Lesson;
use Course\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Subscription\Events\UserProgressed;
use Tests\TestCase;

/**
 * @package Course\Unit\Events
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class UserCompletedALessonTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group  unit
     * @group  unit:course
     * @group  unit:course:event
     * @return void
     */
    public function it_triggers_the_event_when_a_user_completed_a_lesson()
    {
        // Arrangements
        $user = factory(Student::class)->create();
        $course = factory(Course::class)->create();
        $lessons = factory(Lesson::class, 4)->create([
            'course_id' => $course->getKey()
        ])->each->attachToSelf();

        $course->makeLockable();
        $course->publish();
        $course->subscribe($user);

        Event::fake();

        // Actions
        $course->lessons->random()->markAsComplete($user);
        $progress = $course->progressionsOf($user)->first();

        // Assertions
        Event::assertDispatched(UserProgressed::class, function ($e) use ($progress) {
            return $e->progression->id == $progress->id;
        });
    }
}
