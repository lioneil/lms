<?php

namespace Course\Listeners;

use Course\Enumerations\CourseDictionary;
use Course\Services\ProgressionServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Subscription\Events\UserSubscribed;

class CreateUserCourseProgression implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \Subscription\Events\UserSubscribed $event
     * @return void
     */
    public function handle(UserSubscribed $event)
    {
        $student = $event->subscription->user;
        $course = $event->subscription->subscribable;

        $course->progressions()->create([
            'user_id' => $student->getKey(),
            'status' => CourseDictionary::STARTED,
            'metadata' => [
                'lessons' => $course->lessons->mapWithKeys(function ($lesson) use ($course) {
                    return [$lesson->getKey() => [
                        'id' => $lesson->getKey(),
                        'locked' => $course->isLockable() && $this->getInitialLessonLockValue($lesson),
                        'status' => $this->getInitialLessonStatus($lesson),
                        'type' => $lesson->type,
                    ]];
                })->toArray(),
            ],
        ]);
    }

    /**
     * Determine the status of lesson content.
     *
     * @param  \Course\Models\Lesson $lesson
     * @return string
     */
    protected function getInitialLessonStatus($lesson)
    {
        $status = CourseDictionary::PENDING;

        if ($lesson->isFirst()) {
            $status = CourseDictionary::IN_PROGRESS;
        }

        if ($lesson->isSection()) {
            $status = CourseDictionary::NOT_APPLICABLE;
        }

        return $status;
    }

    /**
     * Determine if lesson should be locked.
     *
     * @param  \Course\Models\Lesson $lesson
     * @return string
     */
    protected function getInitialLessonLockValue($lesson)
    {
        return $lesson->isSection() || $lesson->isFirst() ? false : true;
    }
}
