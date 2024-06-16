<?php

Route::prefix('v1/users')->middleware(['auth:api', 'auth.permissions'])->group(function () {
    Route::softDeletes('roles', 'Api\RoleController');
    Route::apiResource('roles', 'Api\RoleController');

    Route::resetResource('permissions', 'Api\ResetPermissionsList');
    Route::get('permissions', 'Api\GetPermissionsList')->name('permissions.index');
});

Route::prefix('v1')->middleware(['auth:api', 'auth.permissions'])->group(function () {
    Route::softDeletes('users', 'Api\UserController');
    Route::apiResource('users', 'Api\UserController');
});

Route::prefix('v1')->middleware(['auth:api'])->group(function () {
    Route::post('permissions/check/{user}', 'Api\CheckUserPermission')->name('permissions.check');
});
