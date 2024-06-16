<?php

namespace Material\Providers;

use Core\Providers\BaseServiceProvider;
use Material\Services\MaterialService;
use Material\Services\MaterialServiceInterface;

class MaterialServiceProvider extends BaseServiceProvider
{
    /**
     * Array of providers to register.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Register services.
     *
     * @return void
     */
    public function registerServiceBindings()
    {
        $this->app->bind(MaterialServiceInterface::class, MaterialService::class);
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
