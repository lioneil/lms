<?php

namespace Setting\Providers;

use Core\Providers\BaseServiceProvider;
use Setting\Http\Routes\SettingsRoutes;
use Setting\Services\SettingService;
use Setting\Services\SettingServiceInterface;

class SettingsServiceProvider extends BaseServiceProvider
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
        parent::register();

        $this->registerRouteMacros();

        $this->registerSettingsBindings();
    }

    /**
     * Register any service class with its interface.
     *
     * @return void
     */
    protected function registerServiceBindings(): void
    {
        $this->app->bind(SettingServiceInterface::class, SettingService::class);
    }

    /**
     * Register the settings bindings class.
     *
     * @return void
     */
    protected function registerSettingsBindings(): void
    {
        $this->app->singleton('settings', function () {
            return new SettingService(new Setting, $this->app['config']['settings'], $this->app['request']);
        });
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

    /**
     * Register comment route macros.
     *
     * @return void
     */
    public function registerRouteMacros(): void
    {
        SettingsRoutes::register();
    }
}
