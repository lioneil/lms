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

Route::prefix('v1')->middleware(['auth:api', 'auth.permissions'])->group(function () {
    Route::post('classrooms/{classroom}/attach', 'Api\AttachClassroom')->name('classrooms.attach');
    Route::patch('classrooms/{classroom}/detach', 'Api\DetachClassroom')->name('classrooms.detach');
    Route::softDeletes('classrooms', 'Api\ClassroomController');
    Route::apiResource('classrooms', 'Api\ClassroomController');
});
