<?php

Route::prefix('v1')->middleware(['auth:api', 'json.force', 'auth.permissions'])->group(function () {
    Route::softDeletes('assignments', 'Api\AssignmentController');
    Route::ownedResource('assignments', 'Api\AssignmentController@owned');
    Route::apiResource('assignments', 'Api\AssignmentController');
});
