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
     * Widget Permissions
     *--------------------------------------------------------------------------
     *
     */
    'index-widgets' => [
        'name' =>  'index-widgets',
        'code' => 'widgets.index',
        'description' => 'Ability to view list of widgets',
        'group' => 'widget',
    ],
    'show-widget' => [
        'name' => 'show-widget',
        'code' => 'widgets.show',
        'description' => 'Ability to show a single widget',
        'group' => 'widget',
    ],
    'store-widget' => [
        'name' => 'store-widget',
        'code' => 'widgets.store',
        'description' => 'Ability to store a widget',
        'group' => 'widget',
    ],
    'update-widget' => [
        'name' => 'update-widget',
        'code' => 'widgets.update',
        'description' => 'Ability to update a widget',
        'group' => 'widget',
    ],
    'refresh-widget' => [
        'name' => 'refresh-widget',
        'code' => 'widgets.refresh',
        'description' => 'Ability to refresh a widget',
        'group' => 'widget',
    ],
    'list-widget' => [
        'name' => 'list-widget',
        'code' => 'widgets.list',
        'description' => 'Ability to list a widget by role',
        'group' => 'widget',
    ],
];
