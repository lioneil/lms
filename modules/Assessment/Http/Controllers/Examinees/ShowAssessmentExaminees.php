<?php

namespace Assessment\Http\Controllers\Examinees;

use Assessment\Services\FieldServiceInterface;
use Core\Http\Controllers\AdminController;

class ShowAssessmentExaminees extends AdminController
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resources = $this->service()->list();

        return view('assessment::examinees.index')->withResources($resources);
    }
}
