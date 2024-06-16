<?php

namespace Core\Application\Repository;

use Core\Application\Repository\Repository;
use Core\Application\Repository\RepositoryInterface;
use Core\Models\Model;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Repository::class, RepositoryInterface::class);
    }
}
