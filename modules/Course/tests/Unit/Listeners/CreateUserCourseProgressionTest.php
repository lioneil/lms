<?php

namespace Course\Unit\Listeners;

use Course\Enumerations\LessonMetadataKeys;
use Course\Listeners\CreateUserCourseProgression;
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
class CreateUserCourseProgressionTest extends TestCase
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
    public function it_triggers_the_listener_when_the_user_subscribed_event_is_fired()
    {
        // Arrangement
        $listener = Mockery::spy(CreateUserCourseProgression::class);
        $this->app->instance(CreateUserCourseProgression::class, $listener);

        $user = factory(Student::class)->create();
        $course = factory(Course::class, 2)->create()->random();
        $course->publish();

        // Actions
        $course->subscribe($user);
        $subscription = $course->subscriptions()->whereUserId($user->getKey())->first();

        // Assertions
        $this->assertTrue($course->isSubscribedBy($user));
        $listener->shouldHaveReceived('handle')->with(Mockery::on(function ($event) use ($subscription) {
            return $event->subscription->id == $subscription->id;
        }))->once();
    }

    /**
     * @test
     * @group  unit
     * @return void
     */
    public function it_can_save_a_course_progression_of_a_student_to_database()
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
        $listener = new CreateUserCourseProgression($this->service);
        $listener->handle(new UserSubscribed($subscription));
        $attributes = [
            'user_id' => $student->getKey(),
            'progressionable_id' => $course->getKey(),
            'progressionable_type' => Course::class,
        ];

        $progression = $this->service
            ->whereUserId($student->getKey())
            ->whereProgressionableId($course->getKey())
            ->first();

        // Assertions
        $this->assertDatabaseHas($this->service->getTable(), $attributes);
        $this->assertTrue(count($progression->metadata['lessons']) == 4);
    }
}
