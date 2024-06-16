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
     * Mail Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-mails' => [
        'name' =>  'index-mails',
        'code' => 'mails.index',
        'description' => 'Ability to view list of mails',
        'group' => 'mail',
    ],
    'show-mail' => [
        'name' => 'show-mail',
        'code' => 'mails.show',
        'description' => 'Ability to show a single mail',
        'group' => 'mail',
    ],
    'create-mail' => [
        'name' => 'create-mail',
        'code' => 'mails.create',
        'description' => 'Ability to create new mail',
        'group' => 'mail',
    ],
    'store-mail' => [
        'name' => 'store-mail',
        'code' => 'mails.store',
        'description' => 'Ability to save the mail',
        'group' => 'mail',
    ],
    'edit-mail' => [
        'name' => 'edit-mail',
        'code' => 'mails.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'mail',
    ],
    'update-mail' => [
        'name' => 'update-mail',
        'code' => 'mails.update',
        'description' => 'Ability to update the mail',
        'group' => 'mail',
    ],
    'destroy-mail' => [
        'name' => 'destroy-mail',
        'code' =>  'mails.destroy',
        'description' => 'Ability to move the mail to trash',
        'group' => 'mail',
    ],
    'delete-mail' => [
        'name' => 'delete-mail',
        'code' =>  'mails.delete',
        'description' => 'Ability to permanently delete the mail',
        'group' => 'mail',
    ],
    'trashed-mails' => [
        'name' => 'trashed-mails',
        'code' =>  'mails.trashed',
        'description' => 'Ability to view the list of all trashed mails',
        'group' => 'mail',
    ],
    'restore-mail' => [
        'name' => 'restore-mail',
        'code' => 'mails.restore',
        'description' => 'Ability to restore the mail from trash',
        'group' => 'mail',
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
    'unrestricted-mail-access' => [
        'name' => 'unrestricted-mail-access',
        'code' => 'mails.unrestricted',
        'description' => 'Ability to edit and delete all mails even if the user is not the creator of the mail.',
        'group' => 'mail',
    ],

    'owned-mails' => [
        'name' => 'owned-mails',
        'code' => 'mails.owned',
        'description' => 'Ability to manage only owned mail.',
        'group' => 'mail',
    ],
];
