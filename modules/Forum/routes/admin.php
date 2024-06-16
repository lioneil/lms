<?php

/**
 *------------------------------------------------------------------------------
 * Admin Routes
 *------------------------------------------------------------------------------
 *
 * Here is where you can register admin routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "admin", "auth.admin", and "auth.permission" middleware group.
 * These routes are available only to authenticated users.
 *
 */

Route::middleware(['breadcrumbs:forum,name'])->group(function () {
    Route::softDeletes('forums', 'ForumController');
    Route::resource('forums', 'ForumController');
});

Route::middleware(['breadcrumbs:thread,name'])->group(function () {
    Route::publishResource('threads', 'ThreadController');
    Route::softDeletes('threads', 'ThreadController');
    Route::resource('threads', 'ThreadController');
});
