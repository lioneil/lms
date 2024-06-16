<?php

namespace Core\Application\Sidebar\Contracts;

use Core\Application\Sidebar\Sidemenu;

interface SidebarInterface
{
    /**
     * Generate the sidebar menus tree.
     *
     * @return self
     */
    public function build();

    /**
     * Retrieve the manifest instance.
     *
     * @return \Core\Manifests\SidebarManifest
     */
    public function manifest();

    /**
     * Retrieve the menus array.
     *
     * @return \Illuminate\Support\Collection
     */
    public function menus();

    /**
     * Retrieve the options array.
     *
     * @return array
     */
    public function options();
}
