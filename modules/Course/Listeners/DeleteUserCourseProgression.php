<?php

namespace Course\Listeners;

use Course\Enumerations\CourseDictionary;
use Course\Services\ProgressionServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Subscription\Events\UserUnsubscribed;

class DeleteUserCourseProgression
{
    /**
     * Handle the event.
     *
     * @param  \Subscription\Events\UserUnsubscribed $event
     * @return void
     */
    public function handle(UserUnsubscribed $event)
    {
        $student = $event->subscription->user;
        $course = $event->subscription->subscribable;

        $course->progressions()->whereUserId($student->getKey())->delete();
    }
}
