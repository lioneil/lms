<?php

namespace Core\Application\Breadcrumbs;

use Core\Application\Breadcrumbs\Breadcrumbs;
use Illuminate\Support\ServiceProvider;

class BreadcrumbsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('breadcrumbs', function ($app) {
            return new Breadcrumbs($app['request']);
        });
    }
}
