<?php

namespace User\Models;

use Core\Models\Accessors\CommonAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use User\Models\Permission;

class Role extends Model
{
    use CommonAttributes,
        Searchable,
        SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get all permissions belonging to role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Retrieve the resource's permission status.
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        return sprintf(
            trans('%s of %s permissions'),
            $this->permissions->count(), Permission::count()
        );
    }
}
