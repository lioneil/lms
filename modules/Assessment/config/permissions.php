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
     * Assessment Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-assessments' => [
        'name' =>  'index-assessments',
        'code' => 'assessments.index',
        'description' => 'Ability to view list of assessments',
        'group' => 'assessment',
    ],
    'show-assessment' => [
        'name' => 'show-assessment',
        'code' => 'assessments.show',
        'description' => 'Ability to show a single assessment',
        'group' => 'assessment',
    ],
    'create-assessment' => [
        'name' => 'create-assessment',
        'code' => 'assessments.create',
        'description' => 'Ability to create new assessment',
        'group' => 'assessment',
    ],
    'store-assessment' => [
        'name' => 'store-assessment',
        'code' => 'assessments.store',
        'description' => 'Ability to save the assessment',
        'group' => 'assessment',
    ],
    'edit-assessment' => [
        'name' => 'edit-assessment',
        'code' => 'assessments.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'assessment',
    ],
    'update-assessment' => [
        'name' => 'update-assessment',
        'code' => 'assessments.update',
        'description' => 'Ability to update the assessment',
        'group' => 'assessment',
    ],
    'destroy-assessment' => [
        'name' => 'destroy-assessment',
        'code' =>  'assessments.destroy',
        'description' => 'Ability to move the assessment to trash',
        'group' => 'assessment',
    ],
    'delete-assessment' => [
        'name' => 'delete-assessment',
        'code' =>  'assessments.delete',
        'description' => 'Ability to permanently delete the assessment',
        'group' => 'assessment',
    ],
    'trashed-assessments' => [
        'name' => 'trashed-assessments',
        'code' =>  'assessments.trashed',
        'description' => 'Ability to view the list of all trashed assessments',
        'group' => 'assessment',
    ],
    'restore-assessment' => [
        'name' => 'restore-assessment',
        'code' => 'assessments.restore',
        'description' => 'Ability to restore the assessment from trash',
        'group' => 'assessment',
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
    'unrestricted-assessment-access' => [
        'name' => 'unrestricted-assessment-access',
        'code' => 'assessments.unrestricted',
        'description' => 'Ability to edit and delete all assessments even if the user is not the creator of the assessment.',
        'group' => 'assessment',
    ],

    'owned-assessments' => [
        'name' => 'owned-assessments',
        'code' => 'assessments.owned',
        'description' => 'Ability to manage only owned assessment.',
        'group' => 'assessment',
    ],

    /**
     *--------------------------------------------------------------------------
     * Submission Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-submissions' => [
        'name' =>  'index-submissions',
        'code' => 'submissions.index',
        'description' => 'Ability to view list of submissions',
        'group' => 'submission',
    ],
    'show-submission' => [
        'name' => 'show-submission',
        'code' => 'submissions.show',
        'description' => 'Ability to show a single submission',
        'group' => 'submission',
    ],
    'create-submission' => [
        'name' => 'create-submission',
        'code' => 'submissions.create',
        'description' => 'Ability to create new submission',
        'group' => 'submission',
    ],
    'store-submission' => [
        'name' => 'store-submission',
        'code' => 'submissions.store',
        'description' => 'Ability to save the submission',
        'group' => 'submission',
    ],
    'edit-submission' => [
        'name' => 'edit-submission',
        'code' => 'submissions.edit',
        'description' => 'Ability to view the edit form',
        'group' => 'submission',
    ],
    'update-submission' => [
        'name' => 'update-submission',
        'code' => 'submissions.update',
        'description' => 'Ability to update the submission',
        'group' => 'submission',
    ],
    'destroy-submission' => [
        'name' => 'destroy-submission',
        'code' =>  'submissions.destroy',
        'description' => 'Ability to move the submission to trash',
        'group' => 'submission',
    ],
    'delete-submission' => [
        'name' => 'delete-submission',
        'code' =>  'submissions.delete',
        'description' => 'Ability to permanently delete the submission',
        'group' => 'submission',
    ],
    'trashed-submissions' => [
        'name' => 'trashed-submissions',
        'code' =>  'submissions.trashed',
        'description' => 'Ability to view the list of all trashed submissions',
        'group' => 'submission',
    ],
    'restore-submission' => [
        'name' => 'restore-submission',
        'code' => 'submissions.restore',
        'description' => 'Ability to restore the submission from trash',
        'group' => 'submission',
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
    'unrestricted-submission-access' => [
        'name' => 'unrestricted-submission-access',
        'code' => 'submissions.unrestricted',
        'description' => 'Ability to edit and delete all submissions even if the user is not the creator of the submission.',
        'group' => 'submission',
    ],

    'owned-submissions' => [
        'name' => 'owned-submissions',
        'code' => 'submissions.owned',
        'description' => 'Ability to manage only owned submission.',
        'group' => 'submission',
    ],

    /**
     *--------------------------------------------------------------------------
     * Export Submission
     *--------------------------------------------------------------------------
     *
     * With these permissions, a submission can be exported, and imported.
     *
     */
    'export-submissions' => [
        'name' => 'export-submissions',
        'code' => 'submissions.export',
        'description' => 'Ability to export submissions.',
        'group' => 'submission',
    ],

    'import-submissions' => [
        'name' => 'import-submissions',
        'code' => 'submissions.import',
        'description' => 'Ability to import submissions.',
        'group' => 'submission',
    ],

    /**
     *--------------------------------------------------------------------------
     * Clone Field
     *--------------------------------------------------------------------------
     *
     * With these permissions, a field can be clone.
     *
     */
    'clone-field' => [
        'name' => 'clone-field',
        'code' => 'fields.clone',
        'description' => 'Ability to clone the field',
        'group' => 'field',
    ],
];
