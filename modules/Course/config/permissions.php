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
     * Course Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-courses' => [
        'name' =>  'index-courses',
        'code' => 'courses.index',
        'description' => 'Ability to view list of courses',
        'group' => 'course',
    ],
    'show-course' => [
        'name' => 'show-course',
        'code' => 'courses.show',
        'description' => 'Ability to show a single course',
        'group' => 'course',
    ],
    'create-course' => [
        'name' => 'create-course',
        'code' => 'courses.create',
        'description' => 'Ability to create new course',
        'group' => 'course',
    ],
    'store-course' => [
        'name' => 'store-course',
        'code' => 'courses.store',
        'description' => 'Ability to save the course',
        'group' => 'course',
    ],
    'edit-course' => [
        'name' => 'edit-course',
        'code' => 'courses.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'course',
    ],
    'update-course' => [
        'name' => 'update-course',
        'code' => 'courses.update',
        'description' => 'Ability to update the course',
        'group' => 'course',
    ],
    'destroy-course' => [
        'name' => 'destroy-course',
        'code' =>  'courses.destroy',
        'description' => 'Ability to move the course to trash',
        'group' => 'course',
    ],
    'delete-course' => [
        'name' => 'delete-course',
        'code' =>  'courses.delete',
        'description' => 'Ability to permanently delete the course',
        'group' => 'course',
    ],
    'trashed-courses' => [
        'name' => 'trashed-courses',
        'code' =>  'courses.trashed',
        'description' => 'Ability to view the list of all trashed courses',
        'group' => 'course',
    ],
    'restore-course' => [
        'name' => 'restore-course',
        'code' => 'courses.restore',
        'description' => 'Ability to restore the course from trash',
        'group' => 'course',
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
    'unrestricted-course-access' => [
        'name' => 'unrestricted-course-access',
        'code' => 'courses.unrestricted',
        'description' => 'Ability to edit and delete all courses even if the user is not the creator of the course.',
        'group' => 'course',
    ],

    'owned-courses' => [
        'name' => 'owned-courses',
        'code' => 'courses.owned',
        'description' => 'Ability to manage only owned course.',
        'group' => 'course',
    ],

    /**
     *--------------------------------------------------------------------------
     * Publishing Access Permissions
     *--------------------------------------------------------------------------
     *
     * With these permissions, a course can be published, unpublished,
     * or drafted.
     *
     */
    'publish-courses' => [
        'name' => 'publish-courses',
        'code' => 'courses.publish',
        'description' => 'Ability to publish a course.',
        'group' => 'course',
    ],

    'unpublish-courses' => [
        'name' => 'unpublish-courses',
        'code' => 'courses.unpublish',
        'description' => 'Ability to unpublish a course.',
        'group' => 'course',
    ],

    'draft-courses' => [
        'name' => 'draft-courses',
        'code' => 'courses.draft',
        'description' => 'Ability to draft a course.',
        'group' => 'course',
    ],

    'expire-courses' => [
        'name' => 'expire-courses',
        'code' => 'courses.expire',
        'description' => 'Ability to expire a course.',
        'group' => 'course',
    ],

    /**
     *--------------------------------------------------------------------------
     * Favorite Course Access Permissions
     *--------------------------------------------------------------------------
     *
     * With these permissions, a course can be favorited, unfavorited,
     * or viewed.
     *
     */
    'favorites-courses' => [
        'name' => 'favorites-courses',
        'code' => 'courses.favorites',
        'description' => 'Ability to view list of favorite courses.',
        'group' => 'course',
    ],

    'favorite-courses' => [
        'name' => 'favorite-courses',
        'code' => 'courses.favorite',
        'description' => 'Ability to favorite a course.',
        'group' => 'course',
    ],

    'unfavorite-courses' => [
        'name' => 'unfavorite-courses',
        'code' => 'courses.unfavorite',
        'description' => 'Ability to unfavorite a course.',
        'group' => 'course',
    ],

    /**
     *--------------------------------------------------------------------------
     * Student Course Access Permissions
     *--------------------------------------------------------------------------
     *
     * With these permissions, a course can be subscribed to, unsubscribed from,
     * or viewed.
     *
     */

    'content-courses' => [
        'name' => 'content-courses',
        'code' => 'courses.content',
        'description' => "Ability to view a subscribed course's contents and lessons.",
        'group' => 'student',
    ],

    'subscriptions-courses' => [
        'name' => 'subscriptions-courses',
        'code' => 'courses.subscriptions',
        'description' => 'Ability to view list of subscribed courses.',
        'group' => 'student',
    ],

    'subscribe-courses' => [
        'name' => 'subscribe-courses',
        'code' => 'courses.subscribe',
        'description' => 'Ability to subscribe a course.',
        'group' => 'student',
    ],

    'unsubscribe-courses' => [
        'name' => 'unsubscribe-courses',
        'code' => 'courses.unsubscribe',
        'description' => 'Ability to unsubscribe a course.',
        'group' => 'student',
    ],

    'progress-courses' => [
        'name' => 'progress-courses',
        'code' => 'courses.progress',
        'description' => 'Ability to track course progress.',
        'group' => 'student',
    ],

    'complete-contents' => [
        'name' => 'complete-contents',
        'code' => 'contents.complete',
        'description' => 'Ability to complete course contents.',
        'group' => 'student',
    ],

    /**
     *--------------------------------------------------------------------------
     * Export Course
     *--------------------------------------------------------------------------
     *
     * With these permissions, a course can be exported, and imported.
     *
     */
    'export-courses' => [
        'name' => 'export-courses',
        'code' => 'courses.export',
        'description' => 'Ability to export courses.',
        'group' => 'course',
    ],

    'import-courses' => [
        'name' => 'import-courses',
        'code' => 'courses.import',
        'description' => 'Ability to import courses.',
        'group' => 'course',
    ],

    /**
     *--------------------------------------------------------------------------
     * Content Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-contents' => [
        'name' =>  'index-contents',
        'code' => 'contents.index',
        'description' => 'Ability to view list of contents',
        'group' => 'content',
    ],
    'show-content' => [
        'name' => 'show-content',
        'code' => 'contents.show',
        'description' => 'Ability to show a single content',
        'group' => 'content',
    ],
    'create-content' => [
        'name' => 'create-content',
        'code' => 'contents.create',
        'description' => 'Ability to create new content',
        'group' => 'content',
    ],
    'store-content' => [
        'name' => 'store-content',
        'code' => 'contents.store',
        'description' => 'Ability to save the content',
        'group' => 'content',
    ],
    'edit-content' => [
        'name' => 'edit-content',
        'code' => 'contents.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'content',
    ],
    'update-content' => [
        'name' => 'update-content',
        'code' => 'contents.update',
        'description' => 'Ability to update the content',
        'group' => 'content',
    ],
    'destroy-content' => [
        'name' => 'destroy-content',
        'code' =>  'contents.destroy',
        'description' => 'Ability to move the content to trash',
        'group' => 'content',
    ],
    'delete-content' => [
        'name' => 'delete-content',
        'code' =>  'contents.delete',
        'description' => 'Ability to permanently delete the content',
        'group' => 'content',
    ],
    'trashed-contents' => [
        'name' => 'trashed-contents',
        'code' =>  'contents.trashed',
        'description' => 'Ability to view the list of all trashed contents',
        'group' => 'content',
    ],
    'restore-content' => [
        'name' => 'restore-content',
        'code' => 'contents.restore',
        'description' => 'Ability to restore the content from trash',
        'group' => 'content',
    ],
    'clone-content' => [
        'name' => 'clone-content',
        'code' => 'contents.clone',
        'description' => 'Ability to clone the content',
        'group' => 'content',
    ],
    'upload-content' => [
        'name' => 'upload-content',
        'code' => 'contents.upload',
        'description' => 'Ability to upload the content',
        'group' => 'content',
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
    'unrestricted-content-access' => [
        'name' => 'unrestricted-content-access',
        'code' => 'contents.unrestricted',
        'description' => 'Ability to edit and delete all contents even if the user is not the creator of the content.',
        'group' => 'content',
    ],

    'owned-contents' => [
        'name' => 'owned-contents',
        'code' => 'contents.owned',
        'description' => 'Ability to manage only owned content.',
        'group' => 'content',
    ],

    'reorder-contents' => [
        'name' => 'reorder-contents',
        'code' => 'contents.reorder',
        'description' => 'Ability to reorder course contents.',
        'group' => 'content',
    ],
];
