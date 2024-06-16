<?php

namespace Classroom\Http\Controllers\Api;

use Classroom\Http\Requests\ClassroomRequest;
use Classroom\Http\Requests\OwnedClassroomRequest;
use Classroom\Http\Resources\ClassroomResource;
use Classroom\Services\ClassroomServiceInterface;
use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class ClassroomController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Classroom\Services\ClassroomServiceInterface $service
     */
    public function __construct(ClassroomServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request\OwnedClassroomRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(OwnedClassroomRequest $request)
    {
        return ClassroomResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\ClassroomRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClassroomRequest $request)
    {
        return response()->json($this->service()->store($request->all()));
    }

     /**
     * Display the specified resource.
     *
     * @param  \Classroom\Http\Requests\OwnedClassroomRequest $request
     * @param  integer                                        $id
     * @return \Illuminate\Http\Response
     */
    public function show(OwnedClassroomRequest $request, int $id)
    {
        return new ClassroomResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Classroom\Http\Requests\ClassroomRequest $request
     * @param  integer                                   $id
     * @return \Illuminate\Http\Response
     */
    public function update(ClassroomRequest $request, int $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Classroom\Http\Requests\OwnedClassroomRequest $request
     * @param  integer                                        $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedClassroomRequest $request, $id)
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
        return ClassroomResource::collection($this->service()->listTrashed());
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Classroom\Http\Requests\OwnedClassroomRequest $request
     * @param  integer|null                                   $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedClassroomRequest $request, $id = null)
    {
        return $this->service()->restore(
            $request->has('id') ? $request->input('id') : $id
        );
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Classroom\Http\Requests\OwnedClassroomRequest $request
     * @param  integer|null                                   $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedClassroomRequest $request, $id = null)
    {
        return $this->service()->delete(
            $request->has('id') ? $request->input('id') : $id
        );
    }
}
