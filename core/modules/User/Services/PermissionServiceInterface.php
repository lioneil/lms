<?php

namespace User\Services;

use Core\Application\Service\ServiceInterface;

interface PermissionServiceInterface extends ServiceInterface
{
    /**
     * Retrieve all permissions
     * from config/permissions.php
     *
     * @return \Illuminate\Support\Collection
     */
    public function permissions();

    /**
     * Update existing or create new permissions
     * from file.
     *
     * @return void
     */
    public function refresh();

    /**
     * Truncate and repopulate the permissions table.
     *
     * @return void
     */
    public function reset();
}
