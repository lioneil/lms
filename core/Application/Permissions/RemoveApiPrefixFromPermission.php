<?php

namespace Core\Application\Permissions;

trait RemoveApiPrefixFromPermission
{
    /**
     * Parse the permission code and remove
     * api prefixes if any.
     *
     * @param  string $permission
     * @return string
     */
    protected function removeApiPrefixFromPermission($permission): string
    {
        $permission = str_replace('api.', '', $permission);
        $permission = explode('.', $permission);

        if (count($permission) >= 3) {
            array_shift($permission);
        }

        $permission = implode('.', $permission);

        return $permission;
    }
}
