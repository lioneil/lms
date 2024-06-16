<?php

namespace Tests;

use Core\Enumerations\Role;
use User\Models\User;
use User\Services\PermissionServiceInterface;
use User\Services\RoleServiceInterface;

trait ActingAsUser
{
    /**
     * Generate a superadmin user.
     *
     * @return \Illuminate\Foundation\Auth\User
     */
    protected function asSuperAdmin()
    {
        $service = $this->app->make(RoleServiceInterface::class);
        $service->import($withSuperAdmin = true);
        $roles = $service->whereCode(Role::SUPERADMIN)->get();

        $user = factory(User::class)->create();
        $user->roles()->attach($roles->pluck('id')->toArray());

        return $user;
    }

    /**
     * Generate a non-superadmin user.
     *
     * @param  string[]|string $permissions
     * @return \Illuminate\Foundation\Auth\User
     */
    protected function asNonSuperAdmin($permissions)
    {
        $permission = $this->app->make(PermissionServiceInterface::class);

        foreach ((array) $permissions as $attribute) {
            $permission->updateOrCreate([
                'code' => $attribute
            ], collect($attribute)->mapWithKeys(function ($code) {
                return [
                    'name' => $code,
                    'group' => $code,
                ];
            })->toArray());
        }

        $service = $this->app->make(RoleServiceInterface::class);
        $service->import(['roles' => ['admin']]);

        $role = $service->whereCode(Role::ADMIN)->first();
        $role->permissions()->sync(
            $permission->whereIn(
                'code', $permissions
            )->pluck('id')->toArray()
        );

        $user = factory(User::class)->state('test')->create();
        $user->roles()->attach($role->getKey());

        return $user;
    }
}
