<?php

namespace User\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use User\Models\Permission;

class RefreshedPermissions
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The PermissionRepository instance.
     *
     * @var \User\Models\Permission
     */
    public $permissions;

    /**
     * Create a new event instance.
     *
     * @param  \User\Models\Permission $permissions
     * @return void
     */
    public function __construct(Permission $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('refresh:permissions');
    }
}
