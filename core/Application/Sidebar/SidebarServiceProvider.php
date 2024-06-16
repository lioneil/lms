<?php

namespace Core\Application\Sidebar;

use Core\Application\Sidebar\Contracts\SidebarInterface;
use Core\Application\Sidebar\Sidebar;
use Core\Application\Sidebar\SidebarComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SidebarServiceProvider extends ServiceProvider
{
    /**
     * Array of view composers.
     *
     * @var array
     */
    protected $composers = [
        [
            'class' => SidebarComposer::class,
            'views' => ['theme::partials.sidebar'],
        ],
    ];

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->composers as $composer) {
            View::composer($composer['views'], $composer['class']);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('manifest:sidebar', 'Core\Manifests\SidebarManifest');
        $this->app->bind(SidebarInterface::class, Sidebar::class);

        $this->app->instance('sidebar', new Sidebar(
            $manifest = $this->app['manifest:sidebar'],
            $this->app['request']
        ));
    }
}
