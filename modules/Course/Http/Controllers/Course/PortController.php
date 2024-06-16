<?php

namespace Course\Http\Controllers\Course;

use Core\Http\Controllers\AdminController;
use Course\Exports\CoursesExport;
use Course\Http\Requests\ExportRequest;
use Course\Services\CourseServiceInterface;
use Illuminate\Http\Request;

class PortController extends AdminController
{
    /**
     * Create a new controller instance.
     *
     * @param \Course\Services\CourseServiceInterface $service
     */
    public function __construct(CourseServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @param  \Course\Http\Requests\ExportRequest $request
     * @return \Illuminate\Http\Response
     */
    public function export(ExportRequest $request)
    {
        return $this->service()->export($request->input('format'));
    }

    /**
     * Export the specified listing of resources.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        return view('theme::admin.index');
    }
}
