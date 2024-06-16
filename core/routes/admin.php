<?php

/**
 *------------------------------------------------------------------------------
 * Admin Routes
 *------------------------------------------------------------------------------
 *
 * Here is where you can register admin routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * contains the "admin" middleware group. Now create something great!
 *
 * All admin routes will have a prefix of "admin".
 * The route below (which is equal to "/admin") will try
 * and redirect to dashboard.
 *
 */

Route::get('/', 'RedirectController@dashboard');
