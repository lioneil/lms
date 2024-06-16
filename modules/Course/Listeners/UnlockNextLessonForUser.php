<?php

namespace Course\Listeners;

use Course\Enumerations\CourseDictionary;
use Course\Services\ContentServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Subscription\Events\UserProgressed;

class UnlockNextLessonForUser
{
    /**
     * The Service class instance.
     *
     * @var \Course\Services\ContentServiceInterface
     */
    protected $service;

    /**
     * Create the event listener.
     *
     * @param  \Course\Services\ContentServiceInterface $service
     * @return void
     */
    public function __construct(ContentServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param  \Subscription\Events\UserProgressed $event
     * @return void
     */
    public function handle(UserProgressed $event)
    {
        $progression = $event->progression;
        $course = $progression->progressionable;
        $user = $progression->user;

        $current = $course->getLessonsWithProgressOf($user)->firstWhere(
            'id', $progression->metadata['recent']
        );

        $next = $course->getNextLesson($current, $user);

        if ($current && $current->isLast() && $current->isCompleted()) {
            $course->markAsComplete($user);
        }

        if ($next && $next->isPending()) {
            $next->markAsInProgress($user);
            $progression->status = CourseDictionary::IN_PROGRESS;
            $progression->save();
        }
    }
}
