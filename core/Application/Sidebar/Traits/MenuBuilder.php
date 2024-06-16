<?php

namespace Core\Application\Sidebar\Traits;

use Codrasil\Tree\Branch;
use Codrasil\Tree\Tree;
use Core\Application\Sidebar\SideMenu;

trait MenuBuilder
{
    /**
     * Initialize the Tree class.
     *
     * @param  mixed $items
     * @param  array $options
     * @return \Codrasil\Tree\Tree
     */
    public function tree($items, array $options = [])
    {
        return new Tree($items, $options);
    }

    /**
     * Array of menus.
     *
     * @param  array $menus
     * @param  array $options
     * @return array
     */
    public function sidemenus(array $menus, array $options)
    {
        foreach ($menus as &$menu) {
            if ($menu->hasChild()) {
                $children = $this->sidemenus($menu->children(), $options);
                $menu->set($options['children'], $children);
                $menu->set($options['active:keys'], $this->mapKey($children, $options['children']));
            }
            $menu = $this->sidemenu($menu);
        }

        return $menus;
    }

    /**
     * Convert the given menu to SideMenu.
     *
     * @param  \Codrasil\Tree\Branch $menu
     * @return \Core\Application\Sidebar\SideMenu
     */
    protected function sidemenu(Branch $menu)
    {
        return new SideMenu($menu, $this->request());
    }

    /**
     * Retrieve the key on each collection.
     *
     * @param  array  $items
     * @param  string $key
     * @return array
     */
    protected function mapKey($items, $key)
    {
        return collect($items)->map(function ($item) use ($items, $key) {
            return array_merge(
                [$item->key('route:name')],
                collect($item->key($key))->map(function ($item) {
                    return $item->key('route:name');
                })->toArray()
            );
        })->flatten(1)->reject(function ($item) {
            return is_null($item);
        })->values()->toArray();
    }
}
