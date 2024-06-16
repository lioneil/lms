<?php

/**
 *------------------------------------------------------------------------------
 * Web Routes
 *------------------------------------------------------------------------------
 *
 * Here is where you can register web routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * contains the "web" middleware group. Now create something great!
 *
 */
Route::get('courses/{courseslug}/{contentslug}', 'Owned\ShowCourseContent')->name('courses.content');

Route::middleware(['auth', 'breadcrumbs:course,title'])->group(function () {
    Route::subscriptionResource('courses', 'Course\SubscriptionController');
});

Route::middleware(['breadcrumbs:course,title'])->group(function () {
    Route::publicResource('courses', 'Web\CourseController');
});
