<?php

namespace Core\Application\Macros;

use Illuminate\Support\Facades\Route;

abstract class RouteMacros
{
    /**
     * Register the route macros.
     *
     * @return void
     */
    public static function register(): void
    {
        static::registerOwnedResource();
        static::registerPortResource();
        static::registerPublicResource();
        static::registerPublishResource();
        static::registerResetResource();
        static::registerSoftDeletes();
    }

    /**
     * Register soft-delete routes.
     *
     * @return void
     */
    protected static function registerSoftDeletes()
    {
        Route::macro('softDeletes', function ($name, $controller) {
            $singular = str_singular($name);

            Route::get(sprintf("%s/trashed", $name), "$controller@trashed")->name("$name.trashed");
            Route::delete(sprintf("%s/delete/{%s?}", $name, $singular), "$controller@delete")->name("$name.delete");
            Route::patch(sprintf("%s/restore/{%s?}", $name, $singular), "$controller@restore")->name("$name.restore");
        });
    }

    /**
     * Register import/export routes.
     *
     * @return void
     */
    protected static function registerPortResource()
    {
        Route::macro('portResource', function ($name, $controller) {
            Route::put(sprintf("%s/import", $name), "$controller@import")->name("$name.import");
            Route::post(sprintf("%s/export", $name), "$controller@export")->name("$name.export");
        });
    }

    /**
     * Register reset/refresh routes.
     *
     * @return void
     */
    protected static function registerResetResource()
    {
        Route::macro('resetResource', function ($name, $controller) {
            Route::post(sprintf("%s/reset", $name), "$controller@reset")->name("$name.reset");
            Route::post(sprintf("%s/refresh", $name), "$controller@refresh")->name("$name.refresh");
        });
    }

    /**
     * Register all/single routes.
     *
     * @return void
     */
    protected static function registerPublicResource()
    {
        Route::macro('publicResource', function ($name, $controller) {
            $singular = sprintf('%sslug', str_singular($name));

            Route::get($name, "$controller@all")->name("$name.all");
            Route::get(sprintf("%s/{%s?}", $name, $singular), "$controller@single")
                ->where($singular, '.*')
                ->name("$name.single");
        });
    }

    /**
     * Register owned routes.
     *
     * @return void
     */
    protected static function registerOwnedResource()
    {
        Route::macro('ownedResource', function ($name, $controller) {
            Route::get(sprintf("%s/owned", $name), "$controller")->name("$name.owned");
        });
    }

    /**
     * Register publishing routes.
     *
     * @return void
     */
    protected static function registerPublishResource()
    {
        Route::macro('publishResource', function ($name, $controller) {
            $singular = str_singular($name);
            Route::post("$name/{".$singular."}/publish", "$controller@publish")->name("$name.publish");
            Route::post("$name/{".$singular."}/unpublish", "$controller@unpublish")->name("$name.unpublish");
            Route::post("$name/{".$singular."}/draft", "$controller@draft")->name("$name.draft");
            Route::patch("$name/{".$singular."}/expire", "$controller@expire")->name("$name.expire");
            Route::get("$name/preview/{".$singular."}", "$controller@preview")->name("$name.preview");
        });
    }
}
