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

Route::prefix('courses')->as('courses.')->middleware(['breadcrumbs:category,name'])->group(function () {
    Route::softDeletes('categories', 'CategoryController');
    Route::resource('categories', 'CategoryController');
});

Route::middleware(['breadcrumbs:course,title'])->group(function () {
    Route::favoriteResource('courses', 'Course\FavoriteController');
    Route::publishResource('courses', 'Course\PublishController');
    Route::ownedResource('courses', 'Owned\ShowOwnedCourses');
    Route::portResource('courses', 'Course\PortController');
    Route::softDeletes('courses', 'CourseController');
    Route::resource('courses', 'CourseController');
});

Route::middleware(['breadcrumbs:discussion,name'])->group(function () {
    Route::softDeletes('discussions', 'DiscussionController');
    Route::resource('discussions', 'DiscussionController');
});
