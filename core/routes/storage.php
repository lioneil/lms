<?php

/**
 *------------------------------------------------------------------------------
 * Storage Routes
 *------------------------------------------------------------------------------
 *
 * Here is where you can register storage routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "web" middleware group. These routes will fetch
 * files from the local storage folder from disk.
 *
 */

Route::get('{file?}', 'StorageController@fetch')
     ->where('file', '.*')
     ->name('storage:fetch');
