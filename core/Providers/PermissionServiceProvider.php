<?php

namespace Core\Providers;

use Core\Application\Permissions\PermissionsPolicy;
use Illuminate\Support\ServiceProvider;
use User\Services\PermissionServiceInterface;
use User\Services\PermissionService;
use User\Services\RoleServiceInterface;
use User\Services\RoleService;
use User\Services\UserServiceInterface;
use User\Services\UserService;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(PermissionServiceInterface::class, PermissionService::class);
        $this->app->bind(RoleServiceInterface::class, RoleService::class);

        $this->app->singleton('core.permissions', function ($app) {
            return new PermissionsPolicy($app[PermissionServiceInterface::class]);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['core.permissions']->bootGateDefinitionsBefore();

        $this->app['core.permissions']->bootGateDefinitions();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['core.permissions'];
    }
}
