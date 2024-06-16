<?php

use Core\Enumerations\Role as RoleCode;

return [
    [
        'name' => ucfirst(RoleCode::SUPERADMIN),
        'code' => strtolower(RoleCode::SUPERADMIN),
        'alias' => ucfirst(RoleCode::SUPERADMIN),
        'description' => 'Super admin users, between other things, can execute maintenance tasks, setup system and so on. Super admin user can setup rights/permissions for other users too, and for admin users',
        'permissions' => [
            '*',
        ],
    ],

    [
        'name' => ucfirst(RoleCode::ADMIN),
        'code' => strtolower(RoleCode::ADMIN),
        'alias' => ucfirst(RoleCode::ADMIN),
        'description' => 'Administrators can execute maintenance tasks, setup system and so on.',
        'permissions' => [
            'permissions.index', 'permissions.refresh', 'permissions.reset',
            'permissions.store', 'permissions.update',

            'roles.create', 'roles.delete', 'roles.destroy', 'roles.edit',
            'roles.index', 'roles.restore', 'roles.show', 'roles.store',
            'roles.trashed', 'roles.trashed', 'roles.update',

            'settings.branding',
            'settings.index', 'settings.store',

            'users.create', 'users.delete', 'users.destroy', 'users.edit', 'users.index',
            'users.profile', 'users.restore', 'users.show', 'users.store',
            'users.trashed', 'users.trashed', 'users.update',
        ],
    ],
];
