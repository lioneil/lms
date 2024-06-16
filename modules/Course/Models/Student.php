<?php

namespace Course\Models;

use Classroom\Models\Classroom;
use Illuminate\Database\Eloquent\Concerns\belongsToMany;
use Illuminate\Database\Eloquent\Model;
use User\Models\Role;
use User\Models\User;

class Student extends User
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Retrieve the course that this lesson belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classroom()
    {
        return $this->belongsToMany(Classroom::class);
    }

    /**
     * Get all roles belonging to user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id');
    }
}
