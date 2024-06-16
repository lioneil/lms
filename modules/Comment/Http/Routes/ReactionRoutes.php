<?php

namespace Comment\Http\Routes;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

abstract class ReactionRoutes
{
    /**
     * Register the route macros.
     *
     * @return void
     */
    public static function register(): void
    {
        Route::macro('reactionResource', function ($name, $controller) {
            $singular = Str::singular($name);

            Route::post("$name/{".$singular."}/like", "$controller@like")->name("$name.like");
            Route::post("$name/{".$singular."}/dislike", "$controller@dislike")->name("$name.dislike");
        });
    }
}
