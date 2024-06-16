<?php

namespace Setting\Http\Routes;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

abstract class SettingsRoutes
{
    /**
     * Register the route macros.
     *
     * @return void
     */
    public static function register(): void
    {
        Route::macro('settingsResource', function ($name, $controller) {
            Route::post("$name/upload", "$controller@upload")->name("$name.upload");
            Route::get($name, "$controller@index")->name("$name.index");
            Route::post($name, "$controller@save")->name("$name.save");
        });
    }
}
