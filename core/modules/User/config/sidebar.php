<?php

return [
    /**
     *--------------------------------------------------------------------------
     * Sidebar Menu
     *--------------------------------------------------------------------------
     *
     * Specify here the menus to appear on the sidebar.
     * Available keys:
     *     name                 - the name of the resource; must be unique,
     *                            otherwise it will override the previous entry
     *     order                - the order of appearance in the sidebar
     *     icon                 - icon to be displayed in the sidebar; the only
     *                            officially supported icon is Material Design Icons
     *                            http://materialdesignicons.com
     *     route:name           - must be an existing route name from routes folder
     *     route:url            - any valid url string, this will override the route:name
     *                            if both keys are present
     *     routes               - an array of route names associated with the menu.
     *                            E.g. If you have a /users as the main sidebar entry, and also:
     *                                  - /users/create
     *                                  - /users/trashed
     *                                  - /users/{user}/edit
     *                                  - /users/{user}
     *                                 Then, when a user visits the above routes,
     *                                 The associated menu will be set to active.
     *     text                 - the label for the sidebar
     *     description          - the title attribute value when rendering to browser
     *     permissions          - an array of values to control user access;
     *                            must be an existing value in any permissions files
     *     parent               - specify the parent of the menu.
     *     children             - an array of sidebar submenus; all keys available to parent menu
     *                            are available for submenus
     *
     * Special Keys:
     *     viewable:always      - if set to true, will ignore the permissions key
     *     viewable:superadmins - If set to false, the superadmin roles will not be able
     *                            to see this entry from the sidebar; by default, it is set to true
     *     is:parent            - specify that the menu entry is a parent menu
     *     is:header            - specify that the menu entry is header type
     *     is:separator         - specify that the menu entry is separator type (usually hr tag)
     *     markup               - specify the HTML markup; only available for header or separator types
     *
     */
    'module:user' => [
        'name' => 'module:user',
        'route:url' => '#',
        'route:name' => 'users.index',
        'routes' => [
            'users.index', 'users.create', 'users.edit', 'users.show', 'users.trashed',
            'roles.index', 'roles.create', 'roles.edit', 'roles.show', 'roles.trashed',
            'permissions.index', 'permissions.create', 'permissions.edit', 'permissions.show', 'permissions.trashed',
        ],
        'order' => 50,
        'icon' => 'mdi mdi-account-outline',
        'text' => 'Users',
        'description' => 'Manage users',
        'children' => [
            'index-users' => [
                'name' => 'index-users',
                'order' => 1,
                'route:name' => 'users.index',
                'routes' => ['users.create', 'users.edit', 'users.show'],
                'permissions' => ['users.index'],
                'text' => 'All Users',
                'description' => 'View the list of all users',
            ],
            'create-user' => [
                'name' => 'create-user',
                'order' => 2,
                'route:name' => 'users.create',
                'permissions' => ['users.create', 'users.store'],
                'text' => 'Add User',
                'description' => 'Create new user',
            ],
            'trashed-user' => [
                'name' => 'trashed-user',
                'order' => 3,
                'route:name' => 'users.trashed',
                'permissions' => ['users.trashed'],
                'text' => 'Deactivated Users',
                'description' => 'View list of all users moved to trash',
            ],

            /**
             *------------------------------------------------------------------
             * Role Module
             *------------------------------------------------------------------
             *
             */
            'module:role' => [
                'name' => 'module:role',
                'order' => 4,
                'route:name' => 'roles.index',
                'icon' => 'mdi mdi-shield-account-outline',
                'permissions' => ['roles.index'],
                'text' => 'Roles',
                'description' => 'View the list of all roles',
                'routes' => ['roles.create', 'roles.edit', 'roles.show', 'roles.trashed'],
            ],

            /**
             *------------------------------------------------------------------
             * Permissions Module
             *------------------------------------------------------------------
             *
             */
            'module:permission' => [
                'name' => 'module:permission',
                'order' => 100,
                'route:name' => 'permissions.index',
                'icon' => 'mdi mdi-shield-key-outline',
                'permissions' => ['permissions.index'],
                'text' => 'Permissions',
                'description' => 'View the list of all permissions',
                'routes' => [
                    'permissions.index', 'permissions.create',
                    'permissions.edit', 'permissions.show', 'permissions.trashed'
                ],
            ],
        ],
    ],
];
