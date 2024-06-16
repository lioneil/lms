<?php

Route::prefix('v1')->middleware(['auth:api', 'json.force', 'bindings', 'auth.permissions'])->group(function () {

    Route::softDeletes('assessments', 'Api\AssessmentController');
    Route::portResource('assessments', 'Api\PortAssessmentController');
    Route::apiResource('assessments', 'Api\AssessmentController');
    Route::patch('fields/reorder', 'Api\ReorderField')->name('fields.reorder');
    Route::apiResource('fields', 'Api\FieldController');
    Route::post('fields/{field}/clone', 'Api\CloneField')->name('fields.clone');
    Route::softDeletes('submissions', 'Api\SubmissionController');
    Route::ownedResource('submissions', 'Api\SubmissionController@owned');
    Route::apiResource('submissions', 'Api\SubmissionController');
});
