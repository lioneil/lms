<?php

namespace User\Listeners;

use Core\Enumerations\Role as RoleCode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use User\Events\RefreshedPermissions;
use User\Services\RoleServiceInterface;

class UpdateSuperadminPermissions
{
    /**
     * The time (seconds) before the job should be processed.
     *
     * @var integer
     */
    public $delay = 60;

    /**
     * The RoleServiceInterface instance.
     *
     * @var \User\Repositories\RoleServiceInterface
     */
    protected $service;

    /**
     * Create the event listener.
     *
     * @param  \User\Services\RoleServiceInterface $service
     * @return void
     */
    public function __construct(RoleServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     * Loop through each superadmin roles and
     * sync all available permissions.
     *
     * @param  \User\Events\RefreshedPermissions $event
     * @return void
     */
    public function handle(RefreshedPermissions $event)
    {
        $this->service->whereIn('code', RoleCode::superadmins())->get()
            ->each(function ($role) use ($event) {
                $role->permissions()->sync($event->permissions->pluck('id')->toArray());
            });
    }
}
