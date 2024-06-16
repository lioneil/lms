<?php

namespace User\Services;

use Core\Application\Service\ServiceInterface;
use User\Models\User;

interface DetailServiceInterface extends ServiceInterface
{
    /**
     * Save the user credentials to the details table
     * if the passed user does not belong to the
     * superadmin group.
     *
     * @param  \User\Models\User $user
     * @param  array             $attributes
     * @return mixed
     */
    public function record(User $user, array $attributes);
}
