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
     * Classroom Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-classrooms' => [
        'name' =>  'index-classrooms',
        'code' => 'classrooms.index',
        'description' => 'Ability to view list of classrooms',
        'group' => 'classroom',
    ],
    'show-classroom' => [
        'name' => 'show-classroom',
        'code' => 'classrooms.show',
        'description' => 'Ability to show a single classroom',
        'group' => 'classroom',
    ],
    'create-classroom' => [
        'name' => 'create-classroom',
        'code' => 'classrooms.create',
        'description' => 'Ability to create new classroom',
        'group' => 'classroom',
    ],
    'store-classroom' => [
        'name' => 'store-classroom',
        'code' => 'classrooms.store',
        'description' => 'Ability to save the classroom',
        'group' => 'classroom',
    ],
    'edit-classroom' => [
        'name' => 'edit-classroom',
        'code' => 'classrooms.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'classroom',
    ],
    'update-classroom' => [
        'name' => 'update-classroom',
        'code' => 'classrooms.update',
        'description' => 'Ability to update the classroom',
        'group' => 'classroom',
    ],
    'destroy-classroom' => [
        'name' => 'destroy-classroom',
        'code' =>  'classrooms.destroy',
        'description' => 'Ability to move the classroom to trash',
        'group' => 'classroom',
    ],
    'delete-classroom' => [
        'name' => 'delete-classroom',
        'code' =>  'classrooms.delete',
        'description' => 'Ability to permanently delete the classroom',
        'group' => 'classroom',
    ],
    'trashed-classrooms' => [
        'name' => 'trashed-classrooms',
        'code' =>  'classrooms.trashed',
        'description' => 'Ability to view the list of all trashed classrooms',
        'group' => 'classroom',
    ],
    'restore-classroom' => [
        'name' => 'restore-classroom',
        'code' => 'classrooms.restore',
        'description' => 'Ability to restore the classroom from trash',
        'group' => 'classroom',
    ],

    'attach-classroom' => [
        'name' => 'attach-classroom',
        'code' => 'classrooms.attach',
        'description' => 'Ability to attach all classrooms',
        'group' => 'classroom',
    ],

    'detach-classroom' => [
        'name' => 'detach-classroom',
        'code' => 'classrooms.detach',
        'description' => 'Ability to detach all classrooms',
        'group' => 'classroom',
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
    'unrestricted-classroom-access' => [
        'name' => 'unrestricted-classroom-access',
        'code' => 'classrooms.unrestricted',
        'description' => 'Ability to edit and delete all classrooms even if the user is not the creator of the classroom.',
        'group' => 'classroom',
    ],

    'owned-classrooms' => [
        'name' => 'owned-classrooms',
        'code' => 'classrooms.owned',
        'description' => 'Ability to manage only owned classroom.',
        'group' => 'classroom',
    ],
];
