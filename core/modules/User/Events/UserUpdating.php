<?php

namespace User\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use User\Models\User;

class UserUpdating
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The updated User model entry.
     *
     * @var \User\Models\User
     */
    public $user;

    /**
     * The original user input values.
     *
     * @var array
     */
    public $attributes;

    /**
     * Create a new event instance.
     *
     * @param  \User\Models\User $user
     * @param  array             $attributes
     * @return void
     */
    public function __construct(User $user, array $attributes = [])
    {
        $this->user = $user;
        $this->attributes = $attributes;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [];
    }
}
