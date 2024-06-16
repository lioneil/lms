<?php

namespace Assessment\Http\Controllers\Api;

use Assessment\Http\Requests\ExportAssessmentRequest;
use Assessment\Http\Requests\ImportRequest;
use Assessment\Services\AssessmentServiceInterface;
use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class PortAssessmentController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Assessment\Services\AssessmentServiceInterface $service
     */
    public function __construct(AssessmentServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Export the resource to browser.
     *
     * @param  \Assessment\Http\Requests\ExportAssessmentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function export(ExportAssessmentRequest $request)
    {
        return $this->service()->export(
            $request->all(), $request->input('format'), $request->input('filename')
        );
    }

    /**
     * Export the resource to browser.
     *
     * @param  \Assessment\Http\Requests\ImportRequest $request
     * @return \Illuminate\Http\Response
     */
    public function import(ImportRequest $request)
    {
        return $this->service()->import($request->file('file'));
    }
}
