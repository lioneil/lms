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
     * Setting Permissions
     *--------------------------------------------------------------------------
     *
     */
    'preferences-settings' => [
        'name' =>  'preferences-settings',
        'code' => 'settings.preferences',
        'description' => 'Ability to view list of personal preferences',
        'group' => 'setting',
    ],
    'branding-settings' => [
        'name' =>  'branding-settings',
        'code' => 'settings.branding',
        'description' => 'Ability to view list of branding options',
        'group' => 'setting',
    ],
    'save-setting' => [
        'name' => 'save-setting',
        'code' => 'settings.save',
        'description' => 'Ability to save settings',
        'group' => 'setting',
    ],
    'store-setting' => [
        'name' => 'store-setting',
        'code' => 'settings.store',
        'description' => 'Ability to save settings',
        'group' => 'setting',
    ],
    'update-setting' => [
        'name' => 'update-setting',
        'code' => 'settings.update',
        'description' => 'Ability to update settings',
        'group' => 'setting',
    ],
    'destroy-setting' => [
        'name' => 'destroy-setting',
        'code' =>  'settings.destroy',
        'description' => 'Ability to delete settings',
        'group' => 'setting',
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
    'unrestricted-setting-access' => [
        'name' => 'unrestricted-setting-access',
        'code' => 'settings.unrestricted',
        'description' => 'Ability to edit and delete all settings even if the user is not the creator of the setting.',
        'group' => 'setting',
    ],

    'owned-settings' => [
        'name' => 'owned-settings',
        'code' => 'settings.owned',
        'description' => 'Ability to manage only owned setting.',
        'group' => 'setting',
    ],
];
