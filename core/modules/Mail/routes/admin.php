<?php

Route::prefix('settings')->middleware(['breadcrumbs:mail,name'])->group(function () {
    Route::softDeletes('mails', 'MailController');
    Route::resource('mails', 'MailController');
});
