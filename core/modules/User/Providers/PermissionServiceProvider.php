<?php

namespace User\Providers;

use Core\Application\Sidebar\SidebarKeys;
use Core\Providers\BaseServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use User\Models\Permission;
use User\Models\Role;
use User\Observers\PermissionObserver;
use User\Observers\RoleObserver;

class PermissionServiceProvider extends BaseServiceProvider
{
    /**
     * Array of observable models.
     *
     * @var array
     */
    protected $observables = [
        [Permission::class => PermissionObserver::class],
        [Role::class => RoleObserver::class],
    ];
}
