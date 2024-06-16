<?php

namespace Comment\Providers;

use Core\Providers\BaseServiceProvider;
use Comment\Events\UserReacted;
use Comment\Listeners\UpdateUserReaction;
use Comment\Services\ReactionService;
use Comment\Services\ReactionServiceInterface;

class EventServiceProvider extends BaseServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserReacted::class => [
            UpdateUserReaction::class,
        ],
    ];

    /**
     * Array of providers to register.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Register any service class with its interface.
     *
     * @return void
     */
    protected function registerServiceBindings()
    {
        $this->app->bind(ReactionServiceInterface::class, ReactionService::class);
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
