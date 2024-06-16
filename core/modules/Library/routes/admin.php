<?php

Route::middleware(['breadcrumbs:library,name'])->group(function () {
    Route::softDeletes('libraries', 'LibraryController');
    Route::resource('libraries', 'LibraryController');
});
