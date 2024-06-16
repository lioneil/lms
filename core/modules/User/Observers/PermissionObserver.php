<?php

namespace User\Observers;

use User\Events\RefreshedPermissions;
use User\Models\Permission;

class PermissionObserver
{
    /**
     * Listen to the Permission refreshed event.
     *
     * @param \User\Models\Permission $permission
     * @return void
     */
    public function refreshed(Permission $permission)
    {
        // Declare all events related to permission refreshed.
        event(new RefreshedPermissions($permission));
    }
}
