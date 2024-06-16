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

    Route::prefix('threads')->as('threads.')->group(function () {
        Route::softDeletes('categories', 'Api\CategoryController');
        Route::ownedResource('categories', 'Api\CategoryController@owned');
        Route::apiResource('categories', 'Api\CategoryController');
    });

    Route::reactionResource('threads', 'Api\ReactionController');
    Route::softDeletes('threads', 'Api\ThreadController');
    Route::ownedResource('threads', 'Api\ThreadController@owned');
    Route::apiResource('threads', 'Api\ThreadController');
});
