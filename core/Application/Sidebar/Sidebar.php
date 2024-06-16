<?php

namespace Core\Application\Sidebar;

use Codrasil\Tree\Tree;
use Core\Manifests\SidebarManifest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Sidebar extends Collection implements Contracts\SidebarInterface
{
    use Traits\MenuBuilder;

    /**
     * The array of items built from config.
     *
     * @var array
     */
    protected $items;

    /**
     * Check if the class has run
     * the build command.
     *
     * @var boolean
     */
    protected $built = false;

    /**
     * The SidebarManifest instance.
     *
     * @var \Core\Manifests\SidebarManifest
     */
    protected $manifest;

    /**
     * The options array to be passed
     * to Codrasil/Tree
     *
     * @var array
     */
    protected $options = [
        'key' => 'name',
        'route:url' => 'route:url',
        'route:name' => 'route:name',
        'children' => 'children',
        'routes' => 'routes',
        'active:keys' => 'active:keys',
    ];

    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Initialize with SidebarManifest and Filesystem
     * to interact with the config/sidebar.php files.
     *
     * @param \Core\Manifests\SidebarManifest $manifest
     * @param \Illuminate\Http\Request        $request
     * @param array                           $options
     */
    public function __construct(SidebarManifest $manifest, Request $request, array $options = [])
    {
        $this->manifest = $manifest;
        $this->request = $request;
        $this->options = array_merge($this->options, $options);
        $this->build();
    }

    /**
     * Retrieve the manifest instance.
     *
     * @return \Core\Manifests\SidebarManifest
     */
    public function manifest()
    {
        return $this->manifest;
    }

    /**
     * Retrieve the http request instance.
     *
     * @return \Illuminate\Http\Request
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * Generate the sidebar items tree.
     *
     * @return self
     */
    public function build()
    {
        if ($this->isBuilt()) {
            return $this;
        }

        $this->items = $this->sidemenus(
            $this->tree(
                $this->manifest()->all()->toArray(),
                $this->options()
            )->get(),
            $this->options()
        );

        $this->built = true;

        return $this;
    }

    /**
     * Retrieve the items array.
     *
     * @return \Illuminate\Support\Collection
     */
    public function menus()
    {
        return $this->items;
    }

    /**
     * Retrieve the options array.
     *
     * @return array
     */
    public function options()
    {
        return $this->options;
    }

    /**
     * Check if the class had already run
     * the build method.
     *
     * @return boolean
     */
    protected function isBuilt()
    {
        return $this->built;
    }
}
