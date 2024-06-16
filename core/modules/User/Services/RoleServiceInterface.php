<?php

namespace User\Services;

use Core\Application\Service\ServiceInterface;

interface RoleServiceInterface extends ServiceInterface
{
    /**
     * Retrieve the default roles
     * from configuration files.
     *
     * @return \Illuminate\Support\Collection
     */
    public function defaults();

    /**
     * Import the specified resources to storage.
     *
     * @param  array $roles
     * @return void
     */
    public function import(array $roles);
}
