<?php

namespace Course\Unit\Listeners;

use Course\Enumerations\LessonMetadataKeys;
use Course\Listeners\DeleteUserCourseProgression;
use Course\Models\Course;
use Course\Models\Lesson;
use Course\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Mockery;
use Subscription\Events\UserUnsubscribed;
use Subscription\Models\Subscription;
use Subscription\Services\ProgressionServiceInterface;
use Tests\TestCase;

/**
 * @package Course\Unit\Listeners
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class DeleteUserCourseProgressionTest extends TestCase
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
    public function it_triggers_the_listener_when_the_user_unsubscribed_event_is_fired()
    {
        // Arrangement
        $listener = Mockery::spy(DeleteUserCourseProgression::class);
        $this->app->instance(DeleteUserCourseProgression::class, $listener);

        $user = factory(Student::class)->create();
        $course = factory(Course::class, 2)->create()->random();
        $course->publish();
        $course->subscribe($user);

        $subscription = $course->subscriptions()->whereUserId($user->getKey())->first();

        // Actions
        $course->unsubscribe($user);

        // Assertions
        $this->assertTrue($course->isUnsubscribedBy($user));
        $listener->shouldHaveReceived('handle')->with(Mockery::on(function ($event) use ($subscription) {
            return $event->subscription->id == $subscription->id;
        }))->once();
    }

    /**
     * @test
     * @group  unit
     * @return void
     */
    public function it_can_remove_a_course_progression_of_a_student_from_database()
    {
        // Arrangements
        $student = factory(Student::class)->create();
        $course = factory(Course::class)->create();

        factory(Lesson::class)->create([
            'course_id' => $course->getKey(),
            'type' => LessonMetadataKeys::SECTION_KEY,
        ]);
        $lessons = factory(Lesson::class, 3)->create(['course_id' => $course->getKey()]);

        $course->publish();

        $subscription = factory(Subscription::class)->create([
            'user_id' => $student->getKey(),
            'subscribable_id' => $course->getKey(),
            'subscribable_type' => Course::class,
        ]);

        // Actions
        $listener = new DeleteUserCourseProgression($this->service);
        $listener->handle(new UserUnsubscribed($subscription));
        $attributes = [
            'user_id' => $student->getKey(),
            'progressionable_id' => $course->getKey(),
            'progressionable_type' => Course::class,
        ];

        // Assertions
        $this->assertDatabaseMissing($this->service->getTable(), $attributes);
    }
}
