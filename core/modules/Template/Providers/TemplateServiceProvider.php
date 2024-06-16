<?php

namespace Template\Providers;

use Template\Services\TemplateService;
use Template\Services\TemplateServiceInterface;
use Core\Providers\BaseServiceProvider;

class TemplateServiceProvider extends BaseServiceProvider
{
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
        $this->app->bind(TemplateServiceInterface::class, TemplateService::class);
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
