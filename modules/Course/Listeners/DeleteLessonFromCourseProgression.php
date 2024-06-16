<?php

namespace Course\Listeners;

use Course\Enumerations\CourseDictionary;
use Course\Events\LessonDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Subscription\Services\ProgressionServiceInterface;

class DeleteLessonFromCourseProgression implements ShouldQueue
{
    /**
     * The Service class instance.
     *
     * @var \Subscription\Services\ProgressionServiceInterface
     */
    protected $service;

    /**
     * Create the event listener.
     *
     * @param  \Subscription\Services\ProgressionServiceInterface $service
     * @return void
     */
    public function __construct(ProgressionServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param  \Course\Events\LessonDeleted $event
     * @return void
     */
    public function handle(LessonDeleted $event)
    {
        $lesson = $event->lesson;
        $course = $lesson->course;

        $progressions = $this->service
            ->whereProgressionableId($course->getKey())
            ->whereProgressionableType(get_class($course))
            ->get();

        $progressions->each(function ($progression) use ($lesson) {
            $progression->metadata = array_merge(
                $progression->metadata->toArray(), [
                    'lessons' => collect(
                        $progression->metadata['lessons']
                    )->forget($lesson->getKey())->toArray()
                ]
            );
            $progression->save();
        });
    }
}
