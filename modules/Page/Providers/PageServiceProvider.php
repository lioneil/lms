<?php

namespace Page\Providers;

use Core\Providers\BaseServiceProvider;
use Page\Services\CategoryService;
use Page\Services\CategoryServiceInterface;
use Page\Services\PageService;
use Page\Services\PageServiceInterface;

class PageServiceProvider extends BaseServiceProvider
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
        $this->app->bind(PageServiceInterface::class, PageService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Retrieve the array of factories.
     *
     * @return array
     */
    public function factories()
    {
        return [
            $this->directory('database/factories'),
        ];
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
