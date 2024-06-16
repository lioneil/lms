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
     * Material Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-materials' => [
        'name' =>  'index-materials',
        'code' => 'materials.index',
        'description' => 'Ability to view list of materials',
        'group' => 'material',
    ],
    'show-material' => [
        'name' => 'show-material',
        'code' => 'materials.show',
        'description' => 'Ability to show a single material',
        'group' => 'material',
    ],
    'create-material' => [
        'name' => 'create-material',
        'code' => 'materials.create',
        'description' => 'Ability to create new material',
        'group' => 'material',
    ],
    'store-material' => [
        'name' => 'store-material',
        'code' => 'materials.store',
        'description' => 'Ability to save the material',
        'group' => 'material',
    ],
    'edit-material' => [
        'name' => 'edit-material',
        'code' => 'materials.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'material',
    ],
    'update-material' => [
        'name' => 'update-material',
        'code' => 'materials.update',
        'description' => 'Ability to update the material',
        'group' => 'material',
    ],
    'destroy-material' => [
        'name' => 'destroy-material',
        'code' =>  'materials.destroy',
        'description' => 'Ability to move the material to trash',
        'group' => 'material',
    ],
    'delete-material' => [
        'name' => 'delete-material',
        'code' =>  'materials.delete',
        'description' => 'Ability to permanently delete the material',
        'group' => 'material',
    ],
    'trashed-materials' => [
        'name' => 'trashed-materials',
        'code' =>  'materials.trashed',
        'description' => 'Ability to view the list of all trashed materials',
        'group' => 'material',
    ],
    'restore-material' => [
        'name' => 'restore-material',
        'code' => 'materials.restore',
        'description' => 'Ability to restore the material from trash',
        'group' => 'material',
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
    'publish-materials' => [
        'name' => 'publish-materials',
        'code' => 'materials.publish',
        'description' => 'Ability to publish a material.',
        'group' => 'material',
    ],

    'unpublish-materials' => [
        'name' => 'unpublish-materials',
        'code' => 'materials.unpublish',
        'description' => 'Ability to unpublish a material.',
        'group' => 'material',
    ],

    'draft-materials' => [
        'name' => 'draft-materials',
        'code' => 'materials.draft',
        'description' => 'Ability to draft a material.',
        'group' => 'material',
    ],

    'expire-materials' => [
        'name' => 'expire-materials',
        'code' => 'materials.expire',
        'description' => 'Ability to expire a material.',
        'group' => 'material',
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
    'unrestricted-material-access' => [
        'name' => 'unrestricted-material-access',
        'code' => 'materials.unrestricted',
        'description' => 'Ability to edit and delete all materials even if the user is not the creator of the material.',
        'group' => 'material',
    ],

    'owned-materials' => [
        'name' => 'owned-materials',
        'code' => 'materials.owned',
        'description' => 'Ability to manage only owned material.',
        'group' => 'material',
    ],
];

