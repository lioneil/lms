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
     * Test Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-tests' => [
        'name' =>  'index-tests',
        'code' => 'tests.index',
        'description' => 'Ability to view list of tests',
        'group' => 'test',
    ],
    'show-test' => [
        'name' => 'show-test',
        'code' => 'tests.show',
        'description' => 'Ability to show a single test',
        'group' => 'test',
    ],
    'create-test' => [
        'name' => 'create-test',
        'code' => 'tests.create',
        'description' => 'Ability to create new test',
        'group' => 'test',
    ],
    'store-test' => [
        'name' => 'store-test',
        'code' => 'tests.store',
        'description' => 'Ability to save the test',
        'group' => 'test',
    ],
    'edit-test' => [
        'name' => 'edit-test',
        'code' => 'tests.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'test',
    ],
    'update-test' => [
        'name' => 'update-test',
        'code' => 'tests.update',
        'description' => 'Ability to update the test',
        'group' => 'test',
    ],
    'destroy-test' => [
        'name' => 'destroy-test',
        'code' =>  'tests.destroy',
        'description' => 'Ability to move the test to trash',
        'group' => 'test',
    ],
    'delete-test' => [
        'name' => 'delete-test',
        'code' =>  'tests.delete',
        'description' => 'Ability to permanently delete the test',
        'group' => 'test',
    ],
    'trashed-tests' => [
        'name' => 'trashed-tests',
        'code' =>  'tests.trashed',
        'description' => 'Ability to view the list of all trashed tests',
        'group' => 'test',
    ],
    'restore-test' => [
        'name' => 'restore-test',
        'code' => 'tests.restore',
        'description' => 'Ability to restore the test from trash',
        'group' => 'test',
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
    'unrestricted-test-access' => [
        'name' => 'unrestricted-test-access',
        'code' => 'tests.unrestricted',
        'description' => 'Ability to edit and delete all tests even if the user is not the creator of the test.',
        'group' => 'test',
    ],

    'owned-tests' => [
        'name' => 'owned-tests',
        'code' => 'tests.owned',
        'description' => 'Ability to manage only owned test.',
        'group' => 'test',
    ],
];
