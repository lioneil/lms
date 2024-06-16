<?php

namespace Course\Events;

use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserSubscribedToCourse
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The course instance.
     *
     * @var \Course\Models\Course
     */
    public $course;

    /**
     * Create a new event instance.
     *
     * @param  \Course\Models\Course $course
     * @return void
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('course:subscription');
    }
}
