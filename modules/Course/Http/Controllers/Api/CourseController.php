<?php

namespace Course\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Course\Http\Requests\CourseRequest;
use Course\Http\Requests\OwnedCourseRequest;
use Course\Http\Resources\CourseResource;
use Course\Services\CourseServiceInterface;

class CourseController extends ApiController
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CourseResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Course\Http\Requests\CourseRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CourseRequest $request)
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
        return new CourseResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Course\Http\Requests\CourseRequest $request
     * @param  integer                             $id
     * @return \Illuminate\Http\Response
     */
    public function update(CourseRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Course\Http\Requests\OwnedCourseRequest $request
     * @param  integer                                  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedCourseRequest $request, $id = null)
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
        return CourseResource::collection($this->service()->listTrashed());
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Course\Http\Requests\OwnedCourseRequest $request
     * @param  integer                                  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedCourseRequest $request, $id = null)
    {
        return $this->service()->restore($request->has('id') ? $request->input('id') : $id);
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Course\Http\Requests\OwnedCourseRequest $request
     * @param  integer|null                             $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedCourseRequest $request, $id = null)
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
        return CourseResource::collection($this->service()->list());
    }
}
