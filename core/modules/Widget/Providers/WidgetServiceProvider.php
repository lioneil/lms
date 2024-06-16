<?php

namespace Widget\Providers;

use Core\Providers\BaseServiceProvider;
use Widget\Services\WidgetService;
use Widget\Services\WidgetServiceInterface;

class WidgetServiceProvider extends BaseServiceProvider
{
    /**
     * Array of providers to register.
     *
     * @var array
     */
    protected $providers = [
        //
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(WidgetServiceInterface::class, WidgetService::class);
    }
}
