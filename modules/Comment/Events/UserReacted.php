<?php

namespace Comment\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserReacted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The PermissionRepository instance.
     *
     * @var \Reaction\Models\Reaction
     */
    public $reactions;

    /**
     *
     * @param  \Comment\Models\Reaction $reactions
     * @return void
     */
    public function __construct(Reaction $reactions)
    {
        $this->reactions = $reactions;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
