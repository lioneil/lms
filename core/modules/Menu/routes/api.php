<?php

/**
 *------------------------------------------------------------------------------
 * App Widgets Route
 *------------------------------------------------------------------------------
 *
 * Here is where you can register API routes for retrieving
 * widgets.
 *
 */

Route::middleware(['auth:api', 'json.force', 'bindings'])->prefix('v1')->group(function () {
    Route::get('menus/locations', 'Api\GetMenuLocations')->name('menus.locations');
    Route::post('menus', 'Api\SaveMenus')->name('menus.save');
});
