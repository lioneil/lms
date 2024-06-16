<?php

namespace Assessment\Http\Controllers\Examinees;

use Assessment\Models\Field;
use Assessment\Services\FieldServiceInterface;
use Core\Http\Controllers\AdminController;

class ShowAssessmentExaminee extends AdminController
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
     * Display the specified resource.
     *
     * @param  \Assessment\Models\Field $field
     * @return \Illuminate\Http\Response
     */
    public function show(Field $field)
    {
        return view('assessment::examinees.show')->withResource($field);
    }
}
