<?php

namespace Core\Providers;

use Carbon\Carbon;
use Core\Http\Guards\AdminGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->registerAdminGuard();

        $this->registerPassportRoutes();
    }

    /**
     * Register the auth:admin guard
     * used in all routes/admin.php middleware.
     *
     * @return void
     */
    protected function registerAdminGuard()
    {
        Auth::extend('admin', function ($app, $name, array $config) {
            return new AdminGuard($app->request, Auth::createUserProvider($config['provider']));
        });
    }

    /**
     * Register any authentication/authorization services
     * for the laravel/passport package.
     *
     * @return void
     */
    protected function registerPassportRoutes()
    {
        try {
            if (theme()->active()->has('spa') && theme()->active()->get('spa')) {
                Passport::routes(function ($router) {
                    $router->forAccessTokens();
                    $router->forPersonalAccessTokens();
                    $router->forTransientTokens();
                });

                Passport::tokensExpireIn(Carbon::now()->addMinutes(settings('token:expiration:minutes', 10)));
                Passport::refreshTokensExpireIn(Carbon::now()->addDays(settings('token:refresh:days', 10)));
            }
        } catch (\Exception $e) {
            unset($e);
        }
    }
}
