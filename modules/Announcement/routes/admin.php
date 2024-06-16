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

Route::middleware(['breadcrumbs:announcement,name'])->group(function () {
    Route::softDeletes('announcements', 'AnnouncementController');
    Route::resource('categories', 'CategoryController');
    Route::resource('announcements', 'AnnouncementController');
    Route::post('autodelete', 'AutoDelete');
});
