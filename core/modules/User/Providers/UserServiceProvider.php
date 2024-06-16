<?php

namespace User\Providers;

use Core\Providers\BaseServiceProvider;
use User\Models\User;
use User\Observers\UserObserver;
use User\Repositories\DetailRepository;
use User\Repositories\DetailRepositoryInterface;
use User\Repositories\UserRepository;
use User\Repositories\UserRepositoryInterface;
use User\Services\DetailService;
use User\Services\DetailServiceInterface;
use User\Services\PermissionService;
use User\Services\PermissionServiceInterface;
use User\Services\RoleService;
use User\Services\RoleServiceInterface;
use User\Services\UserService;
use User\Services\UserServiceInterface;

class UserServiceProvider extends BaseServiceProvider
{
    /**
     * Array of observable models.
     *
     * @var array
     */
    protected $observables = [
        [User::class => UserObserver::class],
    ];

    /**
     * Array of providers to register.
     *
     * @var array
     */
    protected $providers = [
        PermissionServiceProvider::class,
        EventServiceProvider::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(DetailServiceInterface::class, DetailService::class);
        $this->app->bind(PermissionServiceInterface::class, PermissionService::class);
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->loadTranslationFiles();
    }

    /**
     * Register translation files.
     *
     * @return void
     */
    public function loadTranslationFiles()
    {
        $this->loadJsonTranslationsFrom(
            $this->directory('resources/lang/en.json')
        );
    }

    /**
     * Get the base directory.
     *
     * @param  string $path
     * @return string
     */
    protected function directory(string $path = '')
    {
        return dirname(__DIR__).DIRECTORY_SEPARATOR.$path;
    }
}
