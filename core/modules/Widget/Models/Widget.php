<?php

namespace Widget\Models;

use Illuminate\Database\Eloquent\Model;
use User\Models\Role;

class Widget extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var string
     */
    protected $guarded = [];

    /**
     * Get all roles belongs to widget.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
