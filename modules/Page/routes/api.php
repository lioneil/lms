<?php

/**
 *------------------------------------------------------------------------------
 * API Routes
 *------------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
 */

Route::prefix('v1')->middleware(['auth:api', 'json.force', 'auth.permissions', 'bindings'])->group(function () {

    Route::prefix('pages')->as('pages.')->group(function () {
        Route::softDeletes('categories', 'Api\CategoryController');
        Route::apiResource('categories', 'Api\CategoryController');
    });

    Route::publishResource('pages', 'Api\PublishController');
    Route::softDeletes('pages', 'Api\PageController');
    Route::ownedResource('pages', 'Api\PageController@owned');
    Route::apiResource('pages', 'Api\PageController');
});
