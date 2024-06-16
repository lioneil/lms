<?php

namespace Course\Unit\Listeners;

use Course\Enumerations\LessonMetadataKeys;
use Course\Events\LessonDeleted;
use Course\Listeners\DeleteLessonFromCourseProgression;
use Course\Models\Content;
use Course\Models\Course;
use Course\Models\Lesson;
use Course\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Mockery;
use Subscription\Events\UserSubscribed;
use Subscription\Models\Subscription;
use Subscription\Services\ProgressionServiceInterface;
use Tests\TestCase;

/**
 * @package Course\Unit\Listeners
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class DeleteLessonFromCourseProgressionTest extends TestCase
{
    use RefreshDatabase;

    /** Set up the service class */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ProgressionServiceInterface::class);
    }

    /**
     * @test
     * @group  unit
     * @return void
     */
    public function it_triggers_the_listener_when_the_lesson_deleted_event_is_fired()
    {
        // Arrangement
        $listener = Mockery::spy(DeleteLessonFromCourseProgression::class);
        $this->app->instance(DeleteLessonFromCourseProgression::class, $listener);

        $user = factory(Student::class)->create();
        $course = factory(Course::class, 2)->create()->random();
        $lessons = factory(Content::class, 3)->create([
            'course_id' => $course->getKey(),
            'type' => LessonMetadataKeys::VIDEO_KEY,
        ])->each->attachToSelf();
        $course->makeLockable();
        $course->publish();
        $course->subscribe($user);

        // Actions
        $lesson = $course->lessons->random();
        $lesson->delete();

        // Assertions
        $listener->shouldHaveReceived('handle')->with(Mockery::on(function ($event) use ($lesson) {
            return $event->lesson->id == $lesson->id;
        }))->once();
    }

    /**
     * @test
     * @group  unit
     * @return void
     */
    public function it_can_delete_a_lesson_progression_of_a_student_from_database()
    {
        // Arrangements
        $user = factory(Student::class)->create();
        $course = factory(Course::class, 2)->create()->random();
        $lessons = factory(Content::class, 3)->create([
            'course_id' => $course->getKey(),
            'type' => LessonMetadataKeys::VIDEO_KEY,
        ])->each->attachToSelf();

        $course->makeLockable();
        $course->publish();
        $course->subscribe($user);

        // Actions
        $progression = $course->progressionsOf($user)->first();
        $lesson = $course->lessons->random();
        $lesson->delete();
        $listener = new DeleteLessonFromCourseProgression($this->service);
        $listener->handle(new LessonDeleted($lesson));

        $attributes = array_merge(
            $progression->toArray(), [
                'metadata' => json_encode($progression->metadata)
            ]
        );

        // Assertions
        $this->assertDatabaseMissing($this->service->getTable(), $attributes);
    }
}
