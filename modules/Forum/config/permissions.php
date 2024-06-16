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
     * Forum Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-forums' => [
        'name' =>  'index-forums',
        'code' => 'forums.index',
        'description' => 'Ability to view list of forums',
        'group' => 'forum',
    ],
    'show-forum' => [
        'name' => 'show-forum',
        'code' => 'forums.show',
        'description' => 'Ability to show a single forum',
        'group' => 'forum',
    ],
    'create-forum' => [
        'name' => 'create-forum',
        'code' => 'forums.create',
        'description' => 'Ability to create new forum',
        'group' => 'forum',
    ],
    'store-forum' => [
        'name' => 'store-forum',
        'code' => 'forums.store',
        'description' => 'Ability to save the forum',
        'group' => 'forum',
    ],
    'edit-forum' => [
        'name' => 'edit-forum',
        'code' => 'forums.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'forum',
    ],
    'update-forum' => [
        'name' => 'update-forum',
        'code' => 'forums.update',
        'description' => 'Ability to update the forum',
        'group' => 'forum',
    ],
    'destroy-forum' => [
        'name' => 'destroy-forum',
        'code' =>  'forums.destroy',
        'description' => 'Ability to move the forum to trash',
        'group' => 'forum',
    ],
    'delete-forum' => [
        'name' => 'delete-forum',
        'code' =>  'forums.delete',
        'description' => 'Ability to permanently delete the forum',
        'group' => 'forum',
    ],
    'trashed-forums' => [
        'name' => 'trashed-forums',
        'code' =>  'forums.trashed',
        'description' => 'Ability to view the list of all trashed forums',
        'group' => 'forum',
    ],
    'restore-forum' => [
        'name' => 'restore-forum',
        'code' => 'forums.restore',
        'description' => 'Ability to restore the forum from trash',
        'group' => 'forum',
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
    'unrestricted-forum-access' => [
        'name' => 'unrestricted-forum-access',
        'code' => 'forums.unrestricted',
        'description' => 'Ability to edit and delete all forums even if the user is not the creator of the forum.',
        'group' => 'forum',
    ],

    'owned-forums' => [
        'name' => 'owned-forums',
        'code' => 'forums.owned',
        'description' => 'Ability to manage only owned forum.',
        'group' => 'forum',
    ],

    /**
     *--------------------------------------------------------------------------
     * Threads Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-threads' => [
        'name' =>  'index-threads',
        'code' => 'threads.index',
        'description' => 'Ability to view list of threads',
        'group' => 'thread',
    ],
    'show-thread' => [
        'name' => 'show-thread',
        'code' => 'threads.show',
        'description' => 'Ability to show a single thread',
        'group' => 'thread',
    ],
    'create-thread' => [
        'name' => 'create-thread',
        'code' => 'threads.create',
        'description' => 'Ability to create new thread',
        'group' => 'thread',
    ],
    'store-thread' => [
        'name' => 'store-thread',
        'code' => 'threads.store',
        'description' => 'Ability to save the thread',
        'group' => 'thread',
    ],
    'edit-thread' => [
        'name' => 'edit-thread',
        'code' => 'threads.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'thread',
    ],
    'update-thread' => [
        'name' => 'update-thread',
        'code' => 'threads.update',
        'description' => 'Ability to update the thread',
        'group' => 'thread',
    ],
    'destroy-thread' => [
        'name' => 'destroy-thread',
        'code' =>  'threads.destroy',
        'description' => 'Ability to move the thread to trash',
        'group' => 'thread',
    ],
    'delete-thread' => [
        'name' => 'delete-thread',
        'code' =>  'threads.delete',
        'description' => 'Ability to permanently delete the thread',
        'group' => 'thread',
    ],
    'trashed-threads' => [
        'name' => 'trashed-threads',
        'code' =>  'threads.trashed',
        'description' => 'Ability to view the list of all trashed threads',
        'group' => 'thread',
    ],
    'restore-thread' => [
        'name' => 'restore-thread',
        'code' => 'threads.restore',
        'description' => 'Ability to restore the thread from trash',
        'group' => 'thread',
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
    'unrestricted-thread-access' => [
        'name' => 'unrestricted-thread-access',
        'code' => 'threads.unrestricted',
        'description' => 'Ability to edit and delete all threads even if the user is not the creator of the thread.',
        'group' => 'thread',
    ],

    'owned-threads' => [
        'name' => 'owned-threads',
        'code' => 'threads.owned',
        'description' => 'Ability to manage only owned thread.',
        'group' => 'thread',
    ],

    /**
     *--------------------------------------------------------------------------
     * Like and Dislike Permissions
     *--------------------------------------------------------------------------
     *
     */

    'like-threads' => [
        'name' => 'like-threads',
        'code' => 'threads.like',
        'description' => 'Ability to like a thread.',
        'group' => 'thread',
    ],

    'dislike-threads' => [
        'name' => 'dislike-threads',
        'code' => 'threads.dislike',
        'description' => 'Ability to dislike a thread.',
        'group' => 'thread',
    ],
];
