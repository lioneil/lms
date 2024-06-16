<?php

namespace Course\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Course\Http\Requests\ExportRequest;
use Course\Http\Requests\ImportRequest;
use Course\Services\CourseServiceInterface;
use Illuminate\Http\Request;

class PortCourseController extends ApiController
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
     * Export the resource to browser.
     *
     * @param  \Course\Http\Requests\ExportRequest $request
     * @return \Illuminate\Http\Response
     */
    public function export(ExportRequest $request)
    {
        return $this->service()->export(
            $request->all(), $request->input('format'), $request->input('filename')
        );
    }

    /**
     * Export the resource to browser.
     *
     * @param  \Course\Http\Requests\ImportRequest $request
     * @return \Illuminate\Http\Response
     */
    public function import(ImportRequest $request)
    {
        return $this->service()->import($request->file('file'));
    }
}
