<?php

namespace User\Observers;

use User\Events\RefreshedPermissions;
use User\Models\Role;

class RoleObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \User\Models\Role $role
     * @return void
     */
    public function created(Role $role)
    {
        //
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \User\Models\Role $role
     * @return void
     */
    public function updated(Role $role)
    {
        //
    }
}
