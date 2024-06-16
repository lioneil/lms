<?php

namespace Comment\Providers;

use Comment\Http\Routes\ReactionRoutes;
use Comment\Services\CommentService;
use Comment\Services\CommentServiceInterface;
use Core\Providers\BaseServiceProvider;

class CommentServiceProvider extends BaseServiceProvider
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
        $this->app->bind(CommentServiceInterface::class, CommentService::class);
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
     * Register comment route macros.
     *
     * @return void
     */
    public function registerRouteMacros(): void
    {
        ReactionRoutes::register();
    }
}
