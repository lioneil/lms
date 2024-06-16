<?php

namespace Assessment\Http\Controllers\Examinees;

use Assessment\Http\Requests\Examinees\ExamineesExportRequest;
use Assessment\Services\FieldServiceInterface;
use Core\Http\Controllers\AdminController;

class ExportExamineesController extends AdminController
{
    /**
     * Create a new controller instance.
     *
     * @param \Assessment\Services\FieldServiceInterface $service
     */
    public function __construct(FieldServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Export the specified listing of resource.
     *
     * @param  \ExamineesExportRequest $request
     * @return \Illuminate\Http\Response
     */
    public function export(ExamineesExportRequest $request)
    {
        return $this->service()->export($request->input('format'));
    }
}
