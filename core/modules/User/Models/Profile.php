<?php

namespace Core\Modules\User\Models;

use User\Models\User;

class Profile extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
}
