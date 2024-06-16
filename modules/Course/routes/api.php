<?php

Route::prefix('v1/web')->middleware(['json.force', 'bindings'])->group(function () {
    Route::get('courses/{courseslug}/contents/{contentslug}', 'Api\GetCourseContent')->name('courses.content');
    Route::publicResource('courses', 'Api\WebCourseController');
});

Route::prefix('v1')->middleware(['auth:api', 'json.force', 'bindings', 'auth.permissions'])->group(function () {
    Route::prefix('courses')->as('courses.')->group(function () {
        Route::softDeletes('categories', 'Api\CategoryController');
        Route::ownedResource('categories', 'Api\CategoryController@owned');
        Route::apiResource('categories', 'Api\CategoryController');
    });

    Route::prefix('courses')->group(function () {
        Route::patch('contents/reorder', 'Api\ReorderCourseContent')->name('contents.reorder');
        Route::post('contents/upload', 'Api\UploadCourseContent')->name('contents.upload');
        Route::post('contents/{content}/clone', 'Api\CloneCourseContent')->name('contents.clone');
        Route::patch('contents/{content}/complete', 'Api\CompleteCourseContent')->name('contents.complete');
        Route::softDeletes('contents', 'Api\ContentController');
        Route::ownedResource('contents', 'Api\ContentController@owned');
        Route::apiResource('contents', 'Api\ContentController');
    });

    Route::subscriptionResource('courses', 'Api\SubscriptionController');
    Route::progressResource('courses', 'Api\UpdateUserCourseProgress');
    Route::ownedResource('courses', 'Api\CourseController@owned');
    Route::publishResource('courses', 'Api\PublishController');
    Route::portResource('courses', 'Api\PortCourseController');
    Route::softDeletes('courses', 'Api\CourseController');
    Route::apiResource('courses', 'Api\CourseController');
});
