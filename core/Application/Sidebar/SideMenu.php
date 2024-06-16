<?php

namespace Core\Application\Sidebar;

use Codrasil\Tree\Branch;
use Illuminate\Http\Request;

class SideMenu
{
    /**
     * Menu variable instance.
     *
     * @var array
     */
    protected $menu;

    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Add helper methods to the menu variable.
     *
     * @param \Codrasil\Tree\Branch    $menu
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Branch $menu, Request $request)
    {
        $this->menu = $menu;
        $this->request = $request;
    }

    /**
     * Return the node instance.
     *
     * @return object
     */
    public function node()
    {
        return $this->menu;
    }

    /**
     * Retrieve the given key's value.
     *
     * @return mixed
     */
    public function id()
    {
        return str_slug($this->menu->key());
    }

    /**
     * Retrieve the given key's value.
     *
     * @param  string $key
     * @return mixed
     */
    public function key(string $key)
    {
        return $this->menu->{$key};
    }

    /**
     * Check if menu is a parent.
     *
     * @return boolean
     */
    public function hasChild()
    {
        return $this->menu->hasChild();
    }

    /**
     * Check if menu is header, parent, or separator.
     *
     * @param  string $key
     * @return boolean
     */
    public function is(string $key = 'header')
    {
        return $this->key("is:$key");
    }

    /**
     * Check if menu has the key.
     *
     * @param  string $key
     * @return boolean
     */
    public function has(string $key = 'header')
    {
        return ! is_null($this->key($key));
    }

    /**
     * Retrieve the children array.
     *
     * @param  string $key
     * @return array
     */
    public function children($key = null)
    {
        if (! is_null($key)) {
            return $this->menu->children()[$key]->children();
        }

        return $this->menu->children();
    }

    /**
     * Retrieve the sidemenu text.
     *
     * @return string
     */
    public function text()
    {
        return $this->key('text');
    }

    /**
     * Retrieve the sidemenu text.
     *
     * @return string
     */
    public function description()
    {
        return $this->key('description');
    }

    /**
     * Retrieve the url key.
     *
     * @return string
     */
    public function url()
    {
        if (! is_null($this->key('route:name'))) {
            return route($this->key('route:name'));
        }

        return $this->key('route:url') ?? url('#');
    }

    /**
     * Retrieve the URI value.
     *
     * @return string
     */
    public function uri()
    {
        return '/'.($this->request()->route()
            ? $this->request()->route()->uri()
            : null);
    }

    /**
     * Retrieve the sidemenu text.
     *
     * @return string
     */
    public function icon()
    {
        return $this->key('icon');
    }

    /**
     * Retrieve the sidemenu text.
     *
     * @return string
     */
    public function markup()
    {
        return $this->key('markup') ?? 'div';
    }

    /**
     * Retrieve the viewable key value.
     *
     * @param  string $default
     * @return boolean
     */
    public function viewable(string $default = 'always')
    {
        return $this->key("viewable:$default");
    }

    /**
     * Check if the sidemenu is the current route.
     *
     * @return boolean
     */
    public function active()
    {
        $currentUrl = $this->request()->route() ? $this->request()->route()->getName() : '/';
        $routes = $this->key('routes');

        if (is_array($routes) && in_array($currentUrl, $routes)) {
            return true;
        }

        if (in_array($this->uri(), collect($this->key('active:keys'))->map(function ($item) {
            return route($item, [], false);
        })->toArray())) {
            return true;
        }

        return $this->request()->url() === $this->url();
    }

    /**
     * Retrieve the settings menu.
     *
     * @return \Core\Application\Sidebar\SideMenu
     */
    public function sidemenus()
    {
        $item = collect($this->children())->filter(function ($item) {
            return $item->url() === url($this->uri());
        })->first();

        return method_exists($item, 'children') ? $item->children() : [];
    }

    /**
     * Retrieve the http request instance.
     *
     * @return \Illuminate\Http\Request
     */
    protected function request()
    {
        return $this->request;
    }

    /**
     * Try to return the node item if no method match.
     *
     * @param  string $method
     * @param  array  $attributes
     * @return mixed
     */
    public function __call(string $method, array $attributes)
    {
        return $this->key($method) ?? $attributes;
    }
}
