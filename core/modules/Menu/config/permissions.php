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
     * Menu Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-menus' => [
        'name' =>  'index-menus',
        'code' => 'menus.index',
        'description' => 'Ability to view list of menus',
        'group' => 'menu',
    ],
    'show-menu' => [
        'name' => 'show-menu',
        'code' => 'menus.show',
        'description' => 'Ability to show a single menu',
        'group' => 'menu',
    ],
    'create-menu' => [
        'name' => 'create-menu',
        'code' => 'menus.create',
        'description' => 'Ability to create new menu',
        'group' => 'menu',
    ],
    'store-menu' => [
        'name' => 'store-menu',
        'code' => 'menus.store',
        'description' => 'Ability to save the menu',
        'group' => 'menu',
    ],
    'edit-menu' => [
        'name' => 'edit-menu',
        'code' => 'menus.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'menu',
    ],
    'update-menu' => [
        'name' => 'update-menu',
        'code' => 'menus.update',
        'description' => 'Ability to update the menu',
        'group' => 'menu',
    ],
    'destroy-menu' => [
        'name' => 'destroy-menu',
        'code' =>  'menus.destroy',
        'description' => 'Ability to move the menu to trash',
        'group' => 'menu',
    ],
    'delete-menu' => [
        'name' => 'delete-menu',
        'code' =>  'menus.delete',
        'description' => 'Ability to permanently delete the menu',
        'group' => 'menu',
    ],
    'trashed-menus' => [
        'name' => 'trashed-menus',
        'code' =>  'menus.trashed',
        'description' => 'Ability to view the list of all trashed menus',
        'group' => 'menu',
    ],
    'restore-menu' => [
        'name' => 'restore-menu',
        'code' => 'menus.restore',
        'description' => 'Ability to restore the menu from trash',
        'group' => 'menu',
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
    'unrestricted-menu-access' => [
        'name' => 'unrestricted-menu-access',
        'code' => 'menus.unrestricted',
        'description' => 'Ability to edit and delete all menus even if the user is not the creator of the menu.',
        'group' => 'menu',
    ],

    'owned-menus' => [
        'name' => 'owned-menus',
        'code' => 'menus.owned',
        'description' => 'Ability to manage only owned menu.',
        'group' => 'menu',
    ],

    'menus-locations' => [
        'name' => 'menus-locations',
        'code' => 'menus.locations',
        'description' => 'Ability to manage menu location',
        'group' => 'menu',
    ],
];
