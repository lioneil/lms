<?php

namespace Core\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use User\Models\User;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Core\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->bindUserModelToRouteParameter();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapAssetsRoutes();

        $this->mapStorageRoutes();

        $this->mapThemeRoutes();

        $this->mapAdminRoutes();

        $this->mapWebRoutes();
    }

    /**
     * Bind the user model's username
     * to route parameter.
     *
     * @return void
     */
    protected function bindUserModelToRouteParameter()
    {
        $this->app['router']->bind('username', function ($username) {
            return User::whereUsername($username)->firstOrFail();
        });
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
        Route::middleware(config('middleware.web'))
             ->namespace($this->namespace)
             ->group(core_path('routes/web.php'));
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
        Route::prefix('admin')
             ->middleware(config('middleware.admin'))
             ->namespace($this->namespace)
             ->group(core_path('routes/admin.php'));
    }

    /**
     * Define the "storage" routes for the application.
     *
     * These routes are typically for fetching resource from the
     * local storage folder.
     *
     * @return void
     */
    protected function mapStorageRoutes()
    {
        Route::prefix('storage')
             ->middleware(config('middleware.storage', 'web'))
             ->namespace($this->namespace)
             ->group(core_path('routes/storage.php'));
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
        Route::middleware(config('middleware.assets', 'web'))
             ->namespace($this->namespace)
             ->group(core_path('routes/assets.php'));
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
        Route::prefix('api')
             ->as('api.')
             ->middleware(config('middleware.api', 'api'))
             ->namespace($this->namespace)
             ->group(core_path('routes/api.php'));
    }

    /**
     * Define the theme routers for the application.
     *
     * These routes are only activated when the theme is active.
     *
     * @return void
     */
    protected function mapThemeRoutes()
    {
        if (file_exists(theme()->active('routes/web.php'))) {
            Route::namespace($this->namespace)
                 ->group(theme()->active('routes/web.php'));
        }
    }
}
