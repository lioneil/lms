<?php

namespace Course\Observers;

use Course\Events\UserSubscribedToACourse;
use Course\Events\UserUnsubscribedToACourse;
use Course\Models\Course;

class CourseObserver
{
    /**
     * Listen to the Course created event.
     *
     * @param  \Course\Models\Course $course
     * @return void
     */
    public function created(Course $course)
    {
        // Add created events here.
    }
}
