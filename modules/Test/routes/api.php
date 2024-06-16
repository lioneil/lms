<?php

/**
 *------------------------------------------------------------------------------
 * API Routes
 *------------------------------------------------------------------------------
 *
 * Here is where you can register admin routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "admin", "auth.admin", and "auth.permission" middleware group.
 * These routes are available only to authenticated users.
 *
 */

Route::prefix('v1/test')->middleware(['auth:api', 'auth.permissions'])->group(function () {
    Route::softDeletes('users', 'Api\UserController');
    Route::apiResource('users', 'Api\UserController');
});
