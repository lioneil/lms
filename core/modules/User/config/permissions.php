<?php
/**
 *------------------------------------------------------------------------------
 * Permissions Array
 *------------------------------------------------------------------------------
 *
 * Here we define our permissions that you can attach to roles.
 *
 * These permissions corresponds to a counterpart
 * route (found in <this module>/routes/<route-files>.php).
 * All permissionable routes should have a `name` (e.g. 'roles.store')
 * for the role authentication middleware to work.
 *
 */
return [
    /**
     *--------------------------------------------------------------------------
     * User Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-users' => [
        'name' =>  'index-users',
        'code' => 'users.index',
        'description' => 'Ability to view list of users',
        'group' => 'user',
    ],
    'show-user' => [
        'name' => 'show-user',
        'code' => 'users.show',
        'description' => 'Ability to show a single user',
        'group' => 'user',
    ],
    'create-user' => [
        'name' => 'create-user',
        'code' => 'users.create',
        'description' => 'Ability to create new user',
        'group' => 'user',
    ],
    'store-user' => [
        'name' => 'store-user',
        'code' => 'users.store',
        'description' => 'Ability to save the user',
        'group' => 'user',
    ],
    'edit-user' => [
        'name' => 'edit-user',
        'code' => 'users.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'user',
    ],
    'update-user' => [
        'name' => 'update-user',
        'code' => 'users.update',
        'description' => 'Ability to update the user',
        'group' => 'user',
    ],
    'destroy-user' => [
        'name' => 'destroy-user',
        'code' =>  'users.destroy',
        'description' => 'Ability to move the user to trash',
        'group' => 'user',
    ],
    'delete-user' => [
        'name' => 'delete-user',
        'code' =>  'users.delete',
        'description' => 'Ability to permanently delete the user',
        'group' => 'user',
    ],
    'trashed-users' => [
        'name' => 'trashed-users',
        'code' =>  'users.trashed',
        'description' => 'Ability to view the list of all trashed users',
        'group' => 'user',
    ],
    'restore-user' => [
        'name' => 'restore-user',
        'code' => 'users.restore',
        'description' => 'Ability to restore the user from trash',
        'group' => 'user',
    ],
    'profile-user' => [
        'name' => 'profile-user',
        'code' => 'users.profile',
        'description' => 'Ability to view the user profile',
        'group' => 'user',
    ],
    'change-password' => [
        'name' =>  'change-password',
        'code' => 'password.change',
        'description' => 'Ability to change the user password without using the old password',
        'group' => 'user',
    ],

    /**
     *--------------------------------------------------------------------------
     * Limited Access Policies
     *--------------------------------------------------------------------------
     * The policy stated below will limit the users to only interact with
     * resources they created. Using this policy, the resource will usually have
     * a `user_id` field defined. A Policy Class is also required to check
     * authorization.
     *
     * E.g.
     *  1. User1 will only be able to edit/delete their own created pages.
     *  2. User1 will not be able to edit User2's created pages.
     *  3. User1 will not be able to delete User2's created pages.
     *  4. User1 will be able to view other users created pages. Although this can
     *     be set to be otherwise. It will depend on the Policy file.
     */
    'unrestricted-user-access' => [
        'name' => 'unrestricted-user-access',
        'code' => 'users.unrestricted',
        'description' => 'Ability to edit and delete all users even if the user is not the creator of the user.',
        'group' => 'user',
    ],

    'owned-users' => [
        'name' => 'owned-users',
        'code' => 'users.owned',
        'description' => 'Ability to manage only owned user.',
        'group' => 'user',
    ],


    /**
     *--------------------------------------------------------------------------
     * Role Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-roles' => [
        'name' =>  'index-roles',
        'code' => 'roles.index',
        'description' => 'Ability to view list of roles',
        'group' => 'role',
    ],
    'show-role' => [
        'name' => 'show-role',
        'code' => 'roles.show',
        'description' => 'Ability to show a single role',
        'group' => 'role',
    ],
    'create-role' => [
        'name' => 'create-role',
        'code' => 'roles.create',
        'description' => 'Ability to create new role',
        'group' => 'role',
    ],
    'store-role' => [
        'name' => 'store-role',
        'code' => 'roles.store',
        'description' => 'Ability to save the role',
        'group' => 'role',
    ],
    'edit-role' => [
        'name' => 'edit-role',
        'code' => 'roles.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'role',
    ],
    'update-role' => [
        'name' => 'update-role',
        'code' => 'roles.update',
        'description' => 'Ability to update the role',
        'group' => 'role',
    ],
    'destroy-role' => [
        'name' => 'destroy-role',
        'code' =>  'roles.destroy',
        'description' => 'Ability to move the role to trash',
        'group' => 'role',
    ],
    'delete-role' => [
        'name' => 'delete-role',
        'code' =>  'roles.delete',
        'description' => 'Ability to permanently delete the role',
        'group' => 'role',
    ],
    'trashed-roles' => [
        'name' => 'trashed-roles',
        'code' =>  'roles.trashed',
        'description' => 'Ability to view the list of all trashed roles',
        'group' => 'role',
    ],
    'restore-role' => [
        'name' => 'restore-role',
        'code' => 'roles.restore',
        'description' => 'Ability to restore the role from trash',
        'group' => 'role',
    ],

    /**
     *--------------------------------------------------------------------------
     * Limited Access Policies
     *--------------------------------------------------------------------------
     * The policy stated below will limit the roles to only interact with
     * resources they created. Using this policy, the resource will usually have
     * a `role_id` field defined. A Policy Class is also required to check
     * authorization.
     *
     * E.g.
     *  1. Role1 will only be able to edit/delete their own created pages.
     *  2. Role1 will not be able to edit Role2's created pages.
     *  3. Role1 will not be able to delete Role2's created pages.
     *  4. Role1 will be able to view other roles created pages. Although this can
     *     be set to be otherwise. It will depend on the Policy file.
     */
    'unrestricted-role-access' => [
        'name' => 'unrestricted-role-access',
        'code' => 'roles.unrestricted',
        'description' => 'Ability to edit and delete all roles even if the role is not the creator of the role.',
        'group' => 'role',
    ],

    'owned-roles' => [
        'name' => 'owned-roles',
        'code' => 'roles.owned',
        'description' => 'Ability to manage only owned role.',
        'group' => 'role',
    ],


    /**
     *--------------------------------------------------------------------------
     * Permission Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-permissions' => [
        'name' =>  'index-permissions',
        'code' => 'permissions.index',
        'description' => 'Ability to view list of permissions',
        'group' => 'permission',
    ],
    'store-permission' => [
        'name' => 'store-permission',
        'code' => 'permissions.store',
        'description' => 'Ability to save the permission',
        'group' => 'permission',
    ],
    'update-permission' => [
        'name' => 'update-permission',
        'code' => 'permissions.update',
        'description' => 'Ability to update the permission',
        'group' => 'permission',
    ],
    'refresh-permissions' => [
        'name' => 'refresh-permissions',
        'code' => 'permissions.refresh',
        'description' => 'Ability to refresh permissions list.',
        'group' => 'permission',
    ],
    'reset-permissions' => [
        'name' => 'reset-permissions',
        'code' => 'permissions.reset',
        'description' => 'Ability to reinstall permissions.',
        'group' => 'permission',
    ],
];
