<?php

namespace Course\Listeners;

use Course\Enumerations\CourseDictionary;
use Course\Events\LessonSaved;
use Course\Services\ProgressionServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateOrCreateUserCourseProgression implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \Course\Events\LessonSaved $event
     * @return void
     */
    public function handle(LessonSaved $event)
    {
        $course = $event->lesson->course;

        $course->progressions->each(function ($progression) use ($course) {
            $lessons = $course->lessons->mapWithKeys(function ($lesson) use ($progression) {
                return [
                    $lesson->getKey() => [
                        'id' => $lesson->getKey(),
                        'locked' => $lesson->course->isLockable() && $this->getInitialLessonLockValue($lesson),
                        'status' => $this->getInitialLessonStatus($lesson),
                        'type' => $lesson->type,
                    ]
                ];
            });

            $progression->metadata = array_merge($progression->metadata->toArray(), [
                'lessons' => array_merge(
                    $lessons->toArray(),
                    $progression->metadata['lessons'] ?? []
                )
            ]);

            $progression->save();
        });
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
