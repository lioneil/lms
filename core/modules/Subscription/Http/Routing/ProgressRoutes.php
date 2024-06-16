<?php

namespace Subscription\Http\Routing;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

abstract class ProgressRoutes
{
    /**
     * Register the route macros.
     *
     * @return void
     */
    public static function register(): void
    {
        if (! Route::hasMacro('progressResource')) {
            Route::macro('progressResource', function ($name, $controller) {
                $singular = Str::singular($name);

                Route::patch("$name/{".$singular."}/progress", $controller)->name("$name.progress");
            });
        }
    }
}
