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
     * Assignment Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-assignments' => [
        'name' =>  'index-assignments',
        'code' => 'assignments.index',
        'description' => 'Ability to view list of assignments',
        'group' => 'assignment',
    ],
    'show-assignment' => [
        'name' => 'show-assignment',
        'code' => 'assignments.show',
        'description' => 'Ability to show a single assignment',
        'group' => 'assignment',
    ],
    'create-assignment' => [
        'name' => 'create-assignment',
        'code' => 'assignments.create',
        'description' => 'Ability to create new assignment',
        'group' => 'assignment',
    ],
    'store-assignment' => [
        'name' => 'store-assignment',
        'code' => 'assignments.store',
        'description' => 'Ability to save the assignment',
        'group' => 'assignment',
    ],
    'edit-assignment' => [
        'name' => 'edit-assignment',
        'code' => 'assignments.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'assignment',
    ],
    'update-assignment' => [
        'name' => 'update-assignment',
        'code' => 'assignments.update',
        'description' => 'Ability to update the assignment',
        'group' => 'assignment',
    ],
    'destroy-assignment' => [
        'name' => 'destroy-assignment',
        'code' =>  'assignments.destroy',
        'description' => 'Ability to move the assignment to trash',
        'group' => 'assignment',
    ],
    'delete-assignment' => [
        'name' => 'delete-assignment',
        'code' =>  'assignments.delete',
        'description' => 'Ability to permanently delete the assignment',
        'group' => 'assignment',
    ],
    'trashed-assignments' => [
        'name' => 'trashed-assignments',
        'code' =>  'assignments.trashed',
        'description' => 'Ability to view the list of all trashed assignments',
        'group' => 'assignment',
    ],
    'restore-assignment' => [
        'name' => 'restore-assignment',
        'code' => 'assignments.restore',
        'description' => 'Ability to restore the assignment from trash',
        'group' => 'assignment',
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
    'unrestricted-assignment-access' => [
        'name' => 'unrestricted-assignment-access',
        'code' => 'assignments.unrestricted',
        'description' => 'Ability to edit and delete all assignments even if the user is not the creator of the assignment.',
        'group' => 'assignment',
    ],

    'owned-assignments' => [
        'name' => 'owned-assignments',
        'code' => 'assignments.owned',
        'description' => 'Ability to manage only owned assignment.',
        'group' => 'assignment',
    ],
];
