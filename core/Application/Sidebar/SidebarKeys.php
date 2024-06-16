<?php

namespace Core\Application\Sidebar;

abstract class SidebarKeys
{
    /**
     * Valid values for
     * the `permissions` key.
     *
     * Normally:
     *     'permissions' => ['users.index', 'users.create', ...],
     *
     * Special values:
     *     // All permission is valid
     *     // null is also acceptable.
     *     'permissions' => '*', // null
     *
     *     // `dashboard` value should only be used in Dashboard module.
     *     'permissions' => 'dashboard',
     *
     */
    const ASTERISK = '*';
    const DASHBOARD = 'dashboard';

    /**
     * Valid keys for sidebar
     * configuration array.
     *
     */
    const ALWAYS_VIEWABLE = 'always:viewable';
    const CHILDREN = 'children';
    const DESCRIPTION = 'description';
    const HTML_CLASS = 'class';
    const HTML_MARKUP = 'markup';
    const ICON = 'icon';
    const IS_HEADER = 'is:header';
    const NAME = 'name';
    const ORDER = 'order';
    const PERMISSIONS = 'permissions';
    const ROUTE = 'route';
    const ROUTE_NAME = 'route:name';
    const ROUTE_URL = 'route:url';
    const ROUTES = 'routes';
    const TEXT = 'text';

    /**
     * Retrieve the viewable values.
     * This is used in validating if
     * the menu's permission key has these values.
     *
     * All menus with these values are accessible by all
     * user type or roles.
     *
     * @return array
     */
    public static function viewables()
    {
        return [
            self::ASTERISK,
            self::DASHBOARD,
        ];
    }
}
