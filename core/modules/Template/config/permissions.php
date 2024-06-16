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
     * Template Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-templates' => [
        'name' =>  'index-templates',
        'code' => 'templates.index',
        'description' => 'Ability to view list of templates',
        'group' => 'template',
    ],
    'show-template' => [
        'name' => 'show-template',
        'code' => 'templates.show',
        'description' => 'Ability to show a single template',
        'group' => 'template',
    ],
    'create-template' => [
        'name' => 'create-template',
        'code' => 'templates.create',
        'description' => 'Ability to create new template',
        'group' => 'template',
    ],
    'store-template' => [
        'name' => 'store-template',
        'code' => 'templates.store',
        'description' => 'Ability to save the template',
        'group' => 'template',
    ],
    'edit-template' => [
        'name' => 'edit-template',
        'code' => 'templates.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'template',
    ],
    'update-template' => [
        'name' => 'update-template',
        'code' => 'templates.update',
        'description' => 'Ability to update the template',
        'group' => 'template',
    ],
    'destroy-template' => [
        'name' => 'destroy-template',
        'code' =>  'templates.destroy',
        'description' => 'Ability to move the template to trash',
        'group' => 'template',
    ],
    'delete-template' => [
        'name' => 'delete-template',
        'code' =>  'templates.delete',
        'description' => 'Ability to permanently delete the template',
        'group' => 'template',
    ],
    'trashed-templates' => [
        'name' => 'trashed-templates',
        'code' =>  'templates.trashed',
        'description' => 'Ability to view the list of all trashed templates',
        'group' => 'template',
    ],
    'restore-template' => [
        'name' => 'restore-template',
        'code' => 'templates.restore',
        'description' => 'Ability to restore the template from trash',
        'group' => 'template',
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
    'unrestricted-template-access' => [
        'name' => 'unrestricted-template-access',
        'code' => 'templates.unrestricted',
        'description' => 'Ability to edit and delete all templates even if the user is not the creator of the template.',
        'group' => 'template',
    ],

    'owned-templates' => [
        'name' => 'owned-templates',
        'code' => 'templates.owned',
        'description' => 'Ability to manage only owned template.',
        'group' => 'template',
    ],
];
