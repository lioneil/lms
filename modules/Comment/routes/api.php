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

Route::prefix('v1')->middleware(['auth:api', 'auth.permissions', 'bindings'])->group(function () {
    Route::reactionResource('comments', 'Api\ReactionController');
    Route::softDeletes('comments', 'Api\CommentController');
    Route::apiResource('comments', 'Api\CommentController');
});