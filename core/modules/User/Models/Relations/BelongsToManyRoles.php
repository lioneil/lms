<?php

namespace User\Models\Relations;

use Illuminate\Support\Facades\Cache;
use Core\Enumerations\Role as RoleCode;
use User\Models\Role;

trait BelongsToManyRoles
{
    /**
     * Get all roles belonging to user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Retrieve the names of the roles
     * for the user.
     *
     * @return string
     */
    public function getRoleAttribute()
    {
        return $this->roles->implode($this->getNameKey(), '/');
    }

    /**
     * Retrieve the permissions list
     * for the user.
     *
     * @return object
     */
    public function getPermissionsAttribute()
    {
        return $this->roles->map(function ($role) {
            return $role->permissions;
        })->flatten();
    }

    /**
     * Retrieve the column key for the role `name`.
     *
     * @return string
     */
    public function getNameKey(): string
    {
        return 'name';
    }

    /**
     * Retrieve the column key for the role `code`.
     *
     * @return string
     */
    public function getCodeKey(): string
    {
        return 'code';
    }

    /**
     * Check if the role is part of
     * the superadmin group.
     *
     * @return boolean
     */
    public function isSuperAdmin(): bool
    {
        return in_array($this->type, RoleCode::superadmins())
        || $this->roles->whereIn(
            $this->getCodeKey(),
            RoleCode::superadmins()
        )->isNotEmpty();
    }

    /**
     * Check if the role is NOT part of
     * the superadmin group.
     *
     * @return boolean
     */
    public function isNotSuperAdmin(): bool
    {
        return ! $this->isSuperAdmin();
    }

    /**
     * Check if the unrestricted
     * permission exists.
     *
     * @param  string $key
     * @return boolean
     */
    public function isUnrestricted(string $key): bool
    {
        return $this->isPermittedTo("$key.unrestricted");
    }

    /**
     * Check if the unrestricted
     * permission does not exists.
     *
     * @param  string $key
     * @return boolean
     */
    public function isRestricted(string $key): bool
    {
        return ! $this->isUnrestricted($key);
    }

    /**
     * Check if the user role supports
     * the given permission.
     *
     * @param  string $code
     * @return boolean
     */
    public function isPermittedTo(string $code)
    {
        return in_array($code, $this->permissions->pluck($this->getCodeKey())->toArray());
    }
}
