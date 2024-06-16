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
     * Quiz Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-quizzes' => [
        'name' =>  'index-quizzes',
        'code' => 'quizzes.index',
        'description' => 'Ability to view list of quizzes',
        'group' => 'quiz',
    ],
    'show-quiz' => [
        'name' => 'show-quiz',
        'code' => 'quizzes.show',
        'description' => 'Ability to show a single quiz',
        'group' => 'quiz',
    ],
    'create-quiz' => [
        'name' => 'create-quiz',
        'code' => 'quizzes.create',
        'description' => 'Ability to create new quiz',
        'group' => 'quiz',
    ],
    'store-quiz' => [
        'name' => 'store-quiz',
        'code' => 'quizzes.store',
        'description' => 'Ability to save the quiz',
        'group' => 'quiz',
    ],
    'edit-quiz' => [
        'name' => 'edit-quiz',
        'code' => 'quizzes.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'quiz',
    ],
    'update-quiz' => [
        'name' => 'update-quiz',
        'code' => 'quizzes.update',
        'description' => 'Ability to update the quiz',
        'group' => 'quiz',
    ],
    'destroy-quiz' => [
        'name' => 'destroy-quiz',
        'code' =>  'quizzes.destroy',
        'description' => 'Ability to move the quiz to trash',
        'group' => 'quiz',
    ],
    'delete-quiz' => [
        'name' => 'delete-quiz',
        'code' =>  'quizzes.delete',
        'description' => 'Ability to permanently delete the quiz',
        'group' => 'quiz',
    ],
    'trashed-quizzes' => [
        'name' => 'trashed-quizzes',
        'code' =>  'quizzes.trashed',
        'description' => 'Ability to view the list of all trashed quizzes',
        'group' => 'quiz',
    ],
    'restore-quiz' => [
        'name' => 'restore-quiz',
        'code' => 'quizzes.restore',
        'description' => 'Ability to restore the quiz from trash',
        'group' => 'quiz',
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
    'unrestricted-quiz-access' => [
        'name' => 'unrestricted-quiz-access',
        'code' => 'quizzes.unrestricted',
        'description' => 'Ability to edit and delete all quizzes even if the user is not the creator of the quiz.',
        'group' => 'quiz',
    ],

    'owned-quizzes' => [
        'name' => 'owned-quizzes',
        'code' => 'quizzes.owned',
        'description' => 'Ability to manage only owned quiz.',
        'group' => 'quiz',
    ],
];
