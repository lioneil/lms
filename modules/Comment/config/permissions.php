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
     * Comment Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-comments' => [
        'name' =>  'index-comments',
        'code' => 'comments.index',
        'description' => 'Ability to view list of comments',
        'group' => 'comment',
    ],
    'show-comment' => [
        'name' => 'show-comment',
        'code' => 'comments.show',
        'description' => 'Ability to show a single comment',
        'group' => 'comment',
    ],
    'create-comment' => [
        'name' => 'create-comment',
        'code' => 'comments.create',
        'description' => 'Ability to create new comment',
        'group' => 'comment',
    ],
    'store-comment' => [
        'name' => 'store-comment',
        'code' => 'comments.store',
        'description' => 'Ability to save the comment',
        'group' => 'comment',
    ],
    'edit-comment' => [
        'name' => 'edit-comment',
        'code' => 'comments.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'comment',
    ],
    'update-comment' => [
        'name' => 'update-comment',
        'code' => 'comments.update',
        'description' => 'Ability to update the comment',
        'group' => 'comment',
    ],
    'destroy-comment' => [
        'name' => 'destroy-comment',
        'code' =>  'comments.destroy',
        'description' => 'Ability to move the comment to trash',
        'group' => 'comment',
    ],
    'delete-comment' => [
        'name' => 'delete-comment',
        'code' =>  'comments.delete',
        'description' => 'Ability to permanently delete the comment',
        'group' => 'comment',
    ],
    'trashed-comments' => [
        'name' => 'trashed-comments',
        'code' =>  'comments.trashed',
        'description' => 'Ability to view the list of all trashed comments',
        'group' => 'comment',
    ],
    'restore-comment' => [
        'name' => 'restore-comment',
        'code' => 'comments.restore',
        'description' => 'Ability to restore the comment from trash',
        'group' => 'comment',
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
    'unrestricted-comment-access' => [
        'name' => 'unrestricted-comment-access',
        'code' => 'comments.unrestricted',
        'description' => 'Ability to edit and delete all comments even if the user is not the creator of the comment.',
        'group' => 'comment',
    ],

    'owned-comments' => [
        'name' => 'owned-comments',
        'code' => 'comments.owned',
        'description' => 'Ability to manage only owned comment.',
        'group' => 'comment',
    ],

    /**
     *--------------------------------------------------------------------------
     * Like and Dislike Permissions
     *--------------------------------------------------------------------------
     *
     */

    'like-comments' => [
        'name' => 'like-comments',
        'code' => 'comments.like',
        'description' => 'Ability to like a comment.',
        'group' => 'comment',
    ],

    'dislike-comments' => [
        'name' => 'dislike-comments',
        'code' => 'comments.dislike',
        'description' => 'Ability to dislike a comment.',
        'group' => 'comment',
    ],
];
