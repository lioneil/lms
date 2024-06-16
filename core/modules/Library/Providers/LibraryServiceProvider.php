<?php

namespace Library\Providers;

use Core\Providers\BaseServiceProvider;
use Library\Services\LibraryService;
use Library\Services\LibraryServiceInterface;

class LibraryServiceProvider extends BaseServiceProvider
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
    public function register()
    {
        $this->app->bind(LibraryServiceInterface::class, LibraryService::class);
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
