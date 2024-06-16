<?php

namespace Favorite\Http\Routing;

use Illuminate\Support\Facades\Route;

abstract class FavoriteRouteMacro
{
    /**
     * Register the route macros.
     *
     * @return void
     */
    public static function register(): void
    {
        if (! Route::hasMacro('favoriteResource')) {
            Route::macro('favoriteResource', function ($name, $controller) {
                Route::prefix($name)->group(function () use ($name, $controller) {
                    $singular = str_singular($name);
                    Route::get('favorites', "$controller@favorites")->name("$name.favorites");
                    Route::post('{'.$singular.'}/favorite', "$controller@favorite")->name("$name.favorite");
                    Route::patch('{'.$singular.'}/unfavorite', "$controller@unfavorite")->name("$name.unfavorite");
                });
            });
        }
    }
}
