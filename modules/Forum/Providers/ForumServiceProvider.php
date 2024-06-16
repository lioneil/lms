<?php

namespace Forum\Providers;

use Comment\Http\Routes\ReactionRoutes;
use Comment\Services\ReactionService;
use Comment\Services\ReactionServiceInterface;
use Core\Providers\BaseServiceProvider;
use Forum\Services\CategoryService;
use Forum\Services\CategoryServiceInterface;
use Forum\Services\ForumService;
use Forum\Services\ForumServiceInterface;
use Forum\Services\ThreadService;
use Forum\Services\ThreadServiceInterface;

class ForumServiceProvider extends BaseServiceProvider
{
    /**
     * Array of providers to register.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerRouteMacros();
    }

    /**
     * Register any service class with its interface.
     *
     * @return void
     */
    protected function registerServiceBindings()
    {
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(ForumServiceInterface::class, ForumService::class);
        $this->app->bind(ThreadServiceInterface::class, ThreadService::class);
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

    /**
     * Register thread route macros.
     *
     * @return void
     */
    public function registerRouteMacros(): void
    {
        ReactionRoutes::register();
    }
}
