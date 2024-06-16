<?php

Route::prefix('users')->middleware(['breadcrumbs:permission,name'])->group(function () {
    Route::resetResource('permissions', 'PermissionController');
    Route::resource('permissions', 'PermissionController', ['only' => ['index', 'store', 'update']]);
});

Route::prefix('users')->middleware(['breadcrumbs:role,name'])->group(function () {
    Route::put('upload', 'RoleController@upload')->name('roles.upload');
    Route::portResource('roles', 'RoleController');
    Route::softDeletes('roles', 'RoleController');
    Route::resource('roles', 'RoleController');
});

Route::middleware(['breadcrumbs:username,displayname', 'verified'])->group(function () {
    Route::get('profile', 'Admin\RedirectProfile');
    Route::get('profile/{username}', 'Admin\ShowProfile')->name('users.profile');
});

Route::middleware(['breadcrumbs:user,displayname'])->group(function () {
    Route::portResource('users', 'UserController');
    Route::softDeletes('users', 'UserController');
    Route::resource('users', 'UserController');
});
