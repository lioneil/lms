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
     * Library Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-libraries' => [
        'name' =>  'index-libraries',
        'code' => 'libraries.index',
        'description' => 'Ability to view list of libraries',
        'group' => 'library',
    ],
    'show-library' => [
        'name' => 'show-library',
        'code' => 'libraries.show',
        'description' => 'Ability to show a single library',
        'group' => 'library',
    ],
    'create-library' => [
        'name' => 'create-library',
        'code' => 'libraries.create',
        'description' => 'Ability to create new library',
        'group' => 'library',
    ],
    'store-library' => [
        'name' => 'store-library',
        'code' => 'libraries.store',
        'description' => 'Ability to save the library',
        'group' => 'library',
    ],
    'edit-library' => [
        'name' => 'edit-library',
        'code' => 'libraries.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'library',
    ],
    'update-library' => [
        'name' => 'update-library',
        'code' => 'libraries.update',
        'description' => 'Ability to update the library',
        'group' => 'library',
    ],
    'destroy-library' => [
        'name' => 'destroy-library',
        'code' =>  'libraries.destroy',
        'description' => 'Ability to move the library to trash',
        'group' => 'library',
    ],
    'delete-library' => [
        'name' => 'delete-library',
        'code' =>  'libraries.delete',
        'description' => 'Ability to permanently delete the library',
        'group' => 'library',
    ],
    'trashed-libraries' => [
        'name' => 'trashed-libraries',
        'code' =>  'libraries.trashed',
        'description' => 'Ability to view the list of all trashed libraries',
        'group' => 'library',
    ],
    'restore-library' => [
        'name' => 'restore-library',
        'code' => 'libraries.restore',
        'description' => 'Ability to restore the library from trash',
        'group' => 'library',
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
    'unrestricted-library-access' => [
        'name' => 'unrestricted-library-access',
        'code' => 'libraries.unrestricted',
        'description' => 'Ability to edit and delete all libraries even if the user is not the creator of the library.',
        'group' => 'library',
    ],

    'owned-libraries' => [
        'name' => 'owned-libraries',
        'code' => 'libraries.owned',
        'description' => 'Ability to manage only owned library.',
        'group' => 'library',
    ],
];
