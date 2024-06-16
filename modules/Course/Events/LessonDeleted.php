<?php

namespace Course\Events;

use Course\Models\Lesson;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LessonDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The Lesson model instance.
     *
     * @var \Course\Models\Lesson
     */
    public $lesson;

    /**
     * Create a new event instance.
     *
     * @param  \Course\Models\Lesson $lesson
     * @return void
     */
    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('lesson:deleted');
    }
}
