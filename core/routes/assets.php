<?php

/**
 *------------------------------------------------------------------------------
 * Assets Routes
 *------------------------------------------------------------------------------
 *
 * Here is where you can register assets routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "web" middleware group. These routes will fetch
 * files from the active theme folder from disk.
 *
 */

Route::get('vendor/{file?}', 'StorageController@vendor')
     ->where('file', '.*')
     ->name('vendor:fetch');

Route::get('theme/{file?}', 'ThemeController@fetch')
     ->where('file', '.*')
     ->name('theme:fetch');

 Route::get('assets/{file?}', 'AssetController@fetch')
     ->where('file', '.*')
     ->name('assets:fetch');
