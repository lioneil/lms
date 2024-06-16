<?php

/**
 *------------------------------------------------------------------------------
 * Admin Routes
 *------------------------------------------------------------------------------
 *
 * Here is where you can register admin routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "admin", "auth.admin", and "auth.permission" middleware group.
 * These routes are available only to authenticated users.
 *
 */

Route::middleware(['breadcrumbs:assessment,name'])->group(function () {
    Route::get('assessments/{assessment}/examinees', 'Examinees\ShowAssessmentExaminees@index')
    ->name('examinees.index');
    Route::get('assessments/{assessment}/examinees/{examinee}', 'Examinees\ShowAssessmentExaminee@show')
    ->name('examinees.show');
    Route::post('assessments/{assessment}/examinees/xport', 'Examinees\ExportExamineesController@export')
    ->name('examinees.export');
    Route::softDeletes('assessments', 'AssessmentController');
    Route::resource('assessments', 'AssessmentController');
});

Route::middleware(['breadcrumbs:submission,name'])->group(function () {
    Route::get('submissions/xport', 'ExportSubmissionController@export');
    Route::portResource('submissions', 'ExportSubmissionController');
    Route::portResource('submissions', 'ExportSubmissionController');
    Route::softDeletes('submissions', 'SubmissionController');
    Route::resource('submissions', 'SubmissionController');
});
