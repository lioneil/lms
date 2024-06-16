<?php

namespace Core\Providers;

use Core\Application\Macros\RouteMacros;
use Core\Manifests\ModuleManifest;
use Favorite\Http\Routing\FavoriteRouteMacro;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Finder\Finder;

class ModuleServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->registerRouteMacros();

        $this->bindModuleManifestFacade();

        $this->map();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ModuleManifest::class];
    }

    /**
     * Register the ModuleManifest Facade.
     *
     * @return void
     */
    protected function bindModuleManifestFacade()
    {
        $this->app->bind('manifest:module', 'Core\Manifests\ModuleManifest');
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->app[ModuleManifest::class]->modules()->each(function ($module) {
            $this->setCurrentModule($module);

            $this->setRootNamespace($module);

            $this->mergeConfigFromModule($module);

            $this->mapEloquentFactories();

            $this->mapServiceProviders();

            $this->mapRoutes();

            $this->mapViews();
        });
    }

    /**
     * Register each modules service providers.
     *
     * @return void
     */
    protected function mapServiceProviders()
    {
        $providers = glob(
            $this->module['path'].
            DIRECTORY_SEPARATOR.'Providers'.
            DIRECTORY_SEPARATOR.'*ServiceProvider.php'
        );

        foreach ($providers as $path) {
            if (file_exists($path)) {
                $provider = get_namespace($path).'\\'.basename($path, '.php');
                $this->app->register($provider);
            }
        }
    }

    /**
     * Register the view files
     * from modules.
     *
     * @return void
     */
    protected function mapViews()
    {
        $path = $this->module['path'].DIRECTORY_SEPARATOR.'views';

        if (file_exists($path)) {
            $this->loadViewsFrom($path, $this->module['name']);
            $this->loadViewsFrom($path, $this->module['code']);
            $this->loadViewsFrom($path, 'theme');
            $this->loadViewsFrom($path, null);
        }
    }

    /**
     * Register the language files
     * from modules.
     *
     * @return void
     */
    protected function mapEloquentFactories()
    {
        $path = $this->module['path'].
            DIRECTORY_SEPARATOR.'database'.
            DIRECTORY_SEPARATOR.'factories';

        if (is_dir($path)) {
            $this->registerEloquentFactoriesFrom($path);
        }
    }

    /**
     * Define all routes from the modules
     *
     * @return void
     */
    protected function mapRoutes()
    {
        $this->mapAssetsRoutes();

        $this->mapConsoleRoutes();

        $this->mapApiRoutes();

        $this->mapAdminRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Define the "assets" routes for the application.
     *
     * These routes are typically for assets fetching.
     *
     * @return void
     */
    protected function mapAssetsRoutes()
    {
        if (file_exists(
            $path = $this->module['path'].DIRECTORY_SEPARATOR.'routes/assets.php'
        )) {
            Route::middleware('web')
                 ->namespace($this->namespace)
                 ->group($path);
        }
    }

    /**
     * Define the "console" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapConsoleRoutes()
    {
        if (file_exists(
            $path = $this->module['path'].DIRECTORY_SEPARATOR.'routes/console.php'
        )) {
            Route::namespace($this->namespace.'\Http\Controllers')
                 ->group($path);
        }
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        if (file_exists(
            $path = $this->module['path'].DIRECTORY_SEPARATOR.'routes/api.php'
        )) {
            Route::prefix('api')
                 ->as('api.')
                 ->namespace($this->namespace.'\Http\Controllers')
                 ->group($path);
        }
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        if (theme()->active()->get('spa')) {
            return;
        }

        if (file_exists(
            $path = $this->module['path'].DIRECTORY_SEPARATOR.'routes/web.php'
        )) {
            Route::middleware(config('middleware.web', ['web', 'ttl:60']))
                 ->namespace($this->namespace.'\Http\Controllers')
                 ->group($path);
        }
    }

    /**
     * Define the "admin" routes for the application.
     *
     * These routes all receive auth:admin, and all web middlewares.
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        if (theme()->active()->get('spa')) {
            return;
        }

        if (file_exists(
            $path = $this->module['path'].DIRECTORY_SEPARATOR.'routes/admin.php'
        )) {
            Route::prefix('admin')
                 ->middleware(config('middleware.admin', ['web', 'auth:admin']))
                 ->namespace($this->namespace.'\Http\Controllers')
                 ->group($path);
        }
    }

    /**
     * Set the current module of the
     * current iteration.
     *
     * @param array $module
     */
    protected function setCurrentModule($module)
    {
        $this->module = $module;
    }

    /**
     * Set the current module's name of the
     * current iteration as the namespace.
     *
     * @param  array $module
     * @return void
     */
    protected function setRootNamespace($module)
    {
        $this->namespace = $module['namespace'];
    }

    /**
     * Merge the configuration file from the current module
     * to the global config array.
     *
     * @param  array $module
     * @return void
     */
    protected function mergeConfigFromModule($module)
    {
        $path = $module['path'].DIRECTORY_SEPARATOR.'config';
        if (file_exists($path)) {
            foreach (Finder::create()->in($path)->name('*.php') as $file) {
                $name = basename($file->getRealPath(), '.php');
                $this->mergeConfigFrom(
                    $file->getRealPath(),
                    "modules.{$module['code']}.$name"
                );
            }
        }
    }

    /**
     * Register all core route macros.
     *
     * @return void
     */
    protected function registerRouteMacros()
    {
        RouteMacros::register();
        FavoriteRouteMacro::register();
    }
}
