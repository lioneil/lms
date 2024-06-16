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
    'module:widget' => [
        'name' => 'module:widget',
        'route:name' => 'widgets.index',
        'order' => 90,
        'icon' => 'mdi mdi-widgets',
        'text' => 'Widgets',
        'parent' => 'module:setting',
        'description' => 'Manage widgets',
        'routes' => ['widgets.index', 'widgets.show'],
        'permissions' => ['widgets.index'],
    ],
];
