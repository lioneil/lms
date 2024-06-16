<?php

namespace Assessment\Http\Controllers\Api;

use Assessment\Http\Requests\AssessmentRequest;
use Assessment\Http\Requests\OwnedAssessmentRequest;
use Assessment\Http\Resources\AssessmentResource;
use Assessment\Services\AssessmentServiceInterface;
use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class AssessmentController extends ApiController
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
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request\OwnedAssessmentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(OwnedAssessmentRequest $request)
    {
        return AssessmentResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\AssessmentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AssessmentRequest $request)
    {
        return response()->json($this->service()->store($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Assessment\Http\Requests\OwnedAssessmentRequest $request
     * @param  integer                                          $id
     * @return \Illuminate\Http\Response
     */
    public function show(OwnedAssessmentRequest $request, int $id)
    {
        return new AssessmentResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\AssessmentRequest $request
     * @param  integer                            $id
     * @return \Illuminate\Http\Response
     */
    public function update(AssessmentRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Assessment\Http\Requests\OwnedAssessmentRequest $request
     * @param  integer                                          $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedAssessmentRequest $request, $id)
    {
        return response()->json($this->service()->destroy(
            $request->has('id') ? $request->input('id') : $id
        ));
    }

    /**
     * Display a listing of the soft-deleted resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        return AssessmentResource::collection($this->service()->listTrashed());
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Assessment\Http\Requests\OwnedAssessmentRequest $request
     * @param  integer|null                                     $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedAssessmentRequest $request, $id = null)
    {
        return $this->service()->restore($request->has('id') ? $request->input('id') : $id);
    }

    /**
     * Permanently delete the sepcified resource.
     *
     * @param  \Assessment\Http\Requests\OwnedAssessmentRequest $request
     * @param  integer|null                                     $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedAssessmentRequest $request, $id = null)
    {
        return $this->service()->delete($request->has('id') ? $request->input('id') : $id);
    }
}
