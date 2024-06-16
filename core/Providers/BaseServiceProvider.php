<?php

namespace Core\Providers;

use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class BaseServiceProvider extends ServiceProvider
{
    /**
     * Array of observable models.
     *
     * @var array
     */
    protected $observables = [
        //
    ];

    /**
     * Array of providers to register.
     *
     * @var array
     */
    protected $providers = [
        //
    ];

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //
    ];

    /**
     * Array of factories path to register.
     *
     * @var array
     */
    protected $factories = [
        //
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootObservables();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerEloquentFactories();

        $this->registerServiceBindings();

        $this->registerProviders();

        $this->registerPolicies();
    }

    /**
     * Gets the array of observables.
     *
     * @return array
     */
    public function observables()
    {
        return $this->observables;
    }

    /**
     * Retrieve the array of providers.
     *
     * @return array
     */
    public function providers()
    {
        return $this->providers;
    }

    /**
     * Retrieve the array of policies.
     *
     * @return array
     */
    public function policies()
    {
        return $this->policies;
    }

    /**
     * Retrieve the array of factories.
     *
     * @return array
     */
    public function factories()
    {
        return $this->factories;
    }

    /**
     * Bootstraps the Observables.
     *
     * @return void
     */
    public function bootObservables()
    {
        try {
            collect($this->observables())->map(function ($observable) {
                return array_merge(collect($observable)->keys()->toArray(), collect($observable)->values()->toArray());
            })->each(function ($observable) {
                if (Schema::hasTable(with($this->app->make($observable[0]))->getTable())) {
                    $model = $this->app->make($observable[0]);
                    $observer = $this->app->make($observable[1]);
                    $model::observe($observer);
                }
            });
        } catch (\Exception $e) {
            unset($e);
        }
    }

    /**
     * Register array of factories.
     *
     * @return void
     */
    protected function registerEloquentFactories()
    {
        foreach ($this->factories() as $factoryPath) {
            $this->registerEloquentFactoriesFrom($factoryPath);
        }
    }

    /**
     * Register any service class with its interface.
     *
     * @return void
     */
    protected function registerServiceBindings()
    {
        // Register service class with its interface.
    }

    /**
     * Register additional providers
     * specified from the providers variable.
     *
     * @return void
     */
    public function registerProviders()
    {
        foreach ($this->providers() as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Register the specified application's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        foreach ($this->policies() as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    /**
     * Register Eloquent factories.
     *
     * @param  string $path
     * @return void
     */
    protected function registerEloquentFactoriesFrom($path)
    {
        $this->app->make(EloquentFactory::class)->load($path);
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
