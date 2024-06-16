<?php

namespace Subscription\Http\Routing;

use Illuminate\Support\Facades\Route;

abstract class SubscriptionRoutes
{
    /**
     * Register the route macros.
     *
     * @return void
     */
    public static function register(): void
    {
        if (! Route::hasMacro('subscriptionResource')) {
            Route::macro('subscriptionResource', function ($name, $controller) {
                Route::prefix($name)->group(function () use ($name, $controller) {
                    $singular = str_singular($name);

                    Route::get('subscriptions', "$controller@subscriptions")
                        ->name("$name.subscriptions");

                    Route::post('{'.$singular.'}/subscribe', "$controller@subscribe")
                        ->name("$name.subscribe");

                    Route::post('{'.$singular.'}/unsubscribe', "$controller@unsubscribe")
                        ->name("$name.unsubscribe");
                });
            });
        }
    }
}
