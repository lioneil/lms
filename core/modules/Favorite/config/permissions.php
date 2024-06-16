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
     * Favorite Permissions
     *--------------------------------------------------------------------------
     *
     */
    'view-favorites' => [
        'name' =>  'view-favorites',
        'code' => 'favorites.index',
        'description' => 'Ability to list of favorite resources',
        'group' => 'favorite',
    ],
    'favorite' => [
        'name' =>  'favorite',
        'code' => 'favorite',
        'description' => 'Ability to favorite a resource',
        'group' => 'favorite',
    ],
    'unfavorite' => [
        'name' =>  'unfavorite',
        'code' => 'unfavorite',
        'description' => 'Ability to unfavorite a resource',
        'group' => 'unfavorite',
    ],
];
