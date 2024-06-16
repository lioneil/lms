<?php

Route::namespace('Settings')->prefix('settings')->group(function () {
    Route::get('branding', 'ShowBranding')->name('settings.branding');
    Route::get('preferences', 'ShowPreference')->name('settings.preferences');
});

Route::get('settings', 'Settings\RedirectSettings')->name('settings.index');
Route::post('settings', 'SettingsController@store')->name('settings.store');
