<?php

namespace Assessment\Http\Controllers\Api;

use Assessment\Http\Requests\OwnedSubmissionRequest;
use Assessment\Http\Requests\SubmissionRequest;
use Assessment\Http\Resources\SubmissionResource;
use Assessment\Services\SubmissionServiceInterface;
use Core\Http\Controllers\Api\ApiController;

class SubmissionController extends ApiController
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SubmissionResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Assessment\Http\Requests\SubmissionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubmissionRequest $request)
    {
        return $this->service()->store($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return new SubmissionResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Assessment\Http\Requests\SubmissionRequest $request
     * @param  integer                                     $id
     * @return \Illuminate\Http\Response
     */
    public function update(SubmissionRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Assessment\Http\Requests\OwnedSubmissionRequest $request
     * @param  integer                                          $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedSubmissionRequest $request, $id = null)
    {
        return $this->service()->destroy($request->has('id') ? $request->input('id') : $id);
    }

    /**
     * Display a listing of the soft-deleted resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        return SubmissionResource::collection($this->service()->listTrashed());
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Assessment\Http\Requests\OwnedSubmissionRequest $request
     * @param  intger|null                                      $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedSubmissionRequest $request, $id = null)
    {
        return $this->service()->restore($request->has('id') ? $request->input('id') : $id);
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Assessment\Http\Requests\OwnedSubmissionRequest $request
     * @param  integer|null                                     $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedSubmissionRequest $request, $id = null)
    {
        return $this->service()->delete($request->has('id') ? $request->input('id') : $id);
    }

    /**
     * Display a listing of the owned resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function owned()
    {
        return SubmissionResource::collection($this->service()->list());
    }
}
