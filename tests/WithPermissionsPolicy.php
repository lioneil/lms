<?php

namespace Tests;

use Core\Application\Permissions\PermissionsPolicy;

trait WithPermissionsPolicy
{
    /**
     * Boot the gate definitions required for
     * authorizing users via permissions.
     *
     * @return void
     */
    public function withPermissionsPolicy()
    {
        $this->app->make(PermissionsPolicy::class)->bootGateDefinitionsBefore();
        $this->app->make(PermissionsPolicy::class)->bootGateDefinitions();
    }
}
