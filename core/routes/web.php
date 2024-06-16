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

Route::get('logout', 'Auth\LoginController@logout')->name('logout.logout');

Auth::routes(['verify' => true]);

Route::post('app::core/widgets', 'Widgets\ShowWidget')->name('core.widgets.show');

Route::get('home', 'RedirectController@home');
Route::get('/', 'HomeController@index')->name('home');
