<?php

namespace Subscription\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresteChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Subscription\Models\Progression;

class UserProgressed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The progression instance.
     *
     * @var \Subscription\Models\Progression
     */
    public $progression;

    /**
     * Create a new event instance.
     *
     * @param  \Subscription\Models\Progression $progression
     * @return void
     */
    public function __construct(Progression $progression)
    {
        $this->progression = $progression;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('user:progressed');
    }
}
