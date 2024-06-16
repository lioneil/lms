<?php

namespace Assignment\Http\Controllers\Api;

use Assignment\Http\Requests\AssignmentRequest;
use Assignment\Http\Requests\OwnedAssignmentRequest;
use Assignment\Http\Resources\AssignmentResource;
use Assignment\Services\AssignmentServiceInterface;
use Core\Http\Controllers\Api\ApiController;

class AssignmentController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Assignment\Services\AssignmentServiceInterface $service
     */
    public function __construct(AssignmentServiceInterface $service)
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
        return AssignmentResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Assignment\Http\Requests\AssignmentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AssignmentRequest $request)
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
        return new AssignmentResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Assignment\Http\Requests\AssignmentRequest $request
     * @param  integer                                     $id
     * @return \Illuminate\Http\Response
     */
    public function update(AssignmentRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified  resource from storage.
     *
     * @param  \Assignment\Http\Requests\OwnedAssignmentRequest $request
     * @param  integer                                          $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedAssignmentRequest $request, $id = null)
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
        return AssignmentResource::collection($this->service()->listTrashed());
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Assignment\Http\Requests\OwnedAssignmentRequest $request
     * @param  integer|null                                     $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedAssignmentRequest $request, $id = null)
    {
        return $this->service()->restore($request->has('id') ? $request->input('id') : $id);
    }

    /**
     * Permanently delete the sepcified resource.
     *
     * @param  \Assignment\Http\Requests\OwnedAssignmentRequest $request
     * @param  integer|null                                     $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedAssignmentRequest $request, $id = null)
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
        return AssignmentResource::collection($this->service()->list());
    }
}
