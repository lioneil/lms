<?php

namespace Core\Application\Permissions;

use Core\Application\Service\WithService;
use Core\Application\Sidebar\SidebarKeys;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use User\Services\PermissionServiceInterface;

class PermissionsPolicy
{
    use WithService;

    /**
     * Inject the PermissionService class.
     *
     * @param \User\Services\PermissionServiceInterface $service
     */
    public function __construct(PermissionServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Register the gate definitions before running the
     * main gate definitions.
     *
     * @return void
     */
    public function bootGateDefinitionsBefore(): void
    {
        Gate::before(function ($user, $code = null) {
            if ($letThrough = in_array($code, SidebarKeys::viewables())) {
                return $letThrough;
            }

            if ($isSuperAdmin = $user->isSuperAdmin()) {
                return $isSuperAdmin;
            }
        });
    }

    /**
     * Register permissions as Gate definitions.
     *
     * @return void
     */
    public function bootGateDefinitions(): void
    {
        try {
            if (Schema::hasTable($this->service()->getTable())) {
                $this->service()->chunk(200, function ($permissions) {
                    $permissions->each(function ($permission) {
                        Gate::define(
                            $permission->code,
                            function ($user, $resource = null) use ($permission) {
                                return ! is_null($resource)
                                       ? $user->getKey() === ($resource->getUserKey() ?? false)
                                       : $user->isPermittedTo($permission->code);
                            }
                        );
                    });
                });
            }
        } catch (\Exception $e) {
            unset($e);
        }
    }
}
