<?php

namespace Assessment\Http\Controllers;

use Assessment\Http\Requests\ExportRequest;
use Assessment\Services\SubmissionServiceInterface;
use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class ExportSubmissionController extends AdminController
{
    /**
     * Create a new controller instance.
     *
     * @param \Assessment\Services\SubmissionServiceInterface $service
     */
    public function __construct(SubmissionServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Export the specified listing of resources.
     *
     * @param  \Assessment\Http\Requests\ExportRequest $request
     * @return \Illuminate\Http\Response
     */
    public function export(ExportRequest $request)
    {
        return $this->service()->export($request->input('format'));
    }

    /**
     * Import the specified listing of resources.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        return view('theme::admin.index');
    }
}
