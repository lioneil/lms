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
    Route::get('widgets', 'Api\AppWidgets')->name('widgets.index');
    Route::post('refresh', 'Api\RefreshWidgets')->name('widgets.refresh');
    Route::get('widgets/list', 'Api\GetDashboardWidget')->name('widgets.list');
    Route::apiResource('widgets', 'Api\WidgetController', ['only' => ['index', 'show', 'store', 'update']]);
});
