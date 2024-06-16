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
     * Category Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-categories' => [
        'name' =>  'index-categories',
        'code' => 'categories.index',
        'description' => 'Ability to view list of categories',
        'group' => 'category',
    ],
    'show-category' => [
        'name' => 'show-category',
        'code' => 'categories.show',
        'description' => 'Ability to show a single category',
        'group' => 'category',
    ],
    'create-category' => [
        'name' => 'create-category',
        'code' => 'categories.create',
        'description' => 'Ability to create new category',
        'group' => 'category',
    ],
    'store-category' => [
        'name' => 'store-category',
        'code' => 'categories.store',
        'description' => 'Ability to save the category',
        'group' => 'category',
    ],
    'edit-category' => [
        'name' => 'edit-category',
        'code' => 'categories.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'category',
    ],
    'update-category' => [
        'name' => 'update-category',
        'code' => 'categories.update',
        'description' => 'Ability to update the category',
        'group' => 'category',
    ],
    'destroy-category' => [
        'name' => 'destroy-category',
        'code' =>  'categories.destroy',
        'description' => 'Ability to move the category to trash',
        'group' => 'category',
    ],
    'delete-category' => [
        'name' => 'delete-category',
        'code' =>  'categories.delete',
        'description' => 'Ability to permanently delete the category',
        'group' => 'category',
    ],
    'trashed-categories' => [
        'name' => 'trashed-categories',
        'code' =>  'categories.trashed',
        'description' => 'Ability to view the list of all trashed categories',
        'group' => 'category',
    ],
    'restore-category' => [
        'name' => 'restore-category',
        'code' => 'categories.restore',
        'description' => 'Ability to restore the category from trash',
        'group' => 'category',
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
    'unrestricted-category-access' => [
        'name' => 'unrestricted-category-access',
        'code' => 'categories.unrestricted',
        'description' => 'Ability to edit and delete all categories even if the user is not the creator of the category.',
        'group' => 'category',
    ],

    'owned-categories' => [
        'name' => 'owned-categories',
        'code' => 'categories.owned',
        'description' => 'Ability to manage only owned category.',
        'group' => 'category',
    ],

    /**
     *--------------------------------------------------------------------------
     * Tag Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-tags' => [
        'name' =>  'index-tags',
        'code' => 'tags.index',
        'description' => 'Ability to view list of tags',
        'group' => 'tag',
    ],
    'show-tag' => [
        'name' => 'show-tag',
        'code' => 'tags.show',
        'description' => 'Ability to show a single tag',
        'group' => 'tag',
    ],
    'create-tag' => [
        'name' => 'create-tag',
        'code' => 'tags.create',
        'description' => 'Ability to create new tag',
        'group' => 'tag',
    ],
    'store-tag' => [
        'name' => 'store-tag',
        'code' => 'tags.store',
        'description' => 'Ability to save the tag',
        'group' => 'tag',
    ],
    'edit-tag' => [
        'name' => 'edit-tag',
        'code' => 'tags.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'tag',
    ],
    'update-tag' => [
        'name' => 'update-tag',
        'code' => 'tags.update',
        'description' => 'Ability to update the tag',
        'group' => 'tag',
    ],
    'destroy-tag' => [
        'name' => 'destroy-tag',
        'code' =>  'tags.destroy',
        'description' => 'Ability to move the tag to trash',
        'group' => 'tag',
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
    'unrestricted-tag-access' => [
        'name' => 'unrestricted-tag-access',
        'code' => 'tags.unrestricted',
        'description' => 'Ability to edit and delete all tags even if the user is not the creator of the tag.',
        'group' => 'tag',
    ],
];
