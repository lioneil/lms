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
     * Announcement Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-announcements' => [
        'name' =>  'index-announcements',
        'code' => 'announcements.index',
        'description' => 'Ability to view list of announcements',
        'group' => 'announcement',
    ],
    'show-announcement' => [
        'name' => 'show-announcement',
        'code' => 'announcements.show',
        'description' => 'Ability to show a single announcement',
        'group' => 'announcement',
    ],
    'create-announcement' => [
        'name' => 'create-announcement',
        'code' => 'announcements.create',
        'description' => 'Ability to create new announcement',
        'group' => 'announcement',
    ],
    'store-announcement' => [
        'name' => 'store-announcement',
        'code' => 'announcements.store',
        'description' => 'Ability to save the announcement',
        'group' => 'announcement',
    ],
    'edit-announcement' => [
        'name' => 'edit-announcement',
        'code' => 'announcements.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'announcement',
    ],
    'update-announcement' => [
        'name' => 'update-announcement',
        'code' => 'announcements.update',
        'description' => 'Ability to update the announcement',
        'group' => 'announcement',
    ],
    'destroy-announcement' => [
        'name' => 'destroy-announcement',
        'code' =>  'announcements.destroy',
        'description' => 'Ability to move the announcement to trash',
        'group' => 'announcement',
    ],
    'delete-announcement' => [
        'name' => 'delete-announcement',
        'code' =>  'announcements.delete',
        'description' => 'Ability to permanently delete the announcement',
        'group' => 'announcement',
    ],
    'trashed-announcements' => [
        'name' => 'trashed-announcements',
        'code' =>  'announcements.trashed',
        'description' => 'Ability to view the list of all trashed announcements',
        'group' => 'announcement',
    ],
    'restore-announcement' => [
        'name' => 'restore-announcement',
        'code' => 'announcements.restore',
        'description' => 'Ability to restore the announcement from trash',
        'group' => 'announcement',
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
    'unrestricted-announcement-access' => [
        'name' => 'unrestricted-announcement-access',
        'code' => 'announcements.unrestricted',
        'description' => 'Ability to edit and delete all announcements even if the user is not the creator of the announcement.',
        'group' => 'announcement',
    ],

    'owned-announcements' => [
        'name' => 'owned-announcements',
        'code' => 'announcements.owned',
        'description' => 'Ability to manage only owned announcement.',
        'group' => 'announcement',
    ],
];
