<?php

namespace Course\Http\Controllers;

use Core\Http\Controllers\AdminController;
use Course\Http\Requests\CourseRequest;
use Course\Http\Requests\OwnedCourseRequest;
use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Http\Request;

class CourseController extends AdminController
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
        $resources = $this->service()->list();

        return view('course::admin.index')->withResources($resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('course::admin.create')->withService($this->service());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Course\Http\Requests\CourseRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CourseRequest $request)
    {
        $this->service()->store($request->all());

        return redirect()->route('courses.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Course\Models\Course $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        return view('course::admin.show')->withResource($course);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Course\Http\Requests\OwnedCourseRequest $request
     * @param  \Course\Models\Course                    $course
     * @return \Illuminate\Http\Response
     */
    public function edit(OwnedCourseRequest $request, Course $course)
    {
        return view('course::admin.edit')->withResource($course)->withService($this->service());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Course\Http\Requests\CourseRequest $request
     * @param  \Course\Models\Course               $course
     * @return \Illuminate\Http\Response
     */
    public function update(CourseRequest $request, Course $course)
    {
        $this->service()->update($course->getKey(), $request->all());

        return redirect()->route('courses.show', $course->getKey());
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
        $this->service()->destroy($request->has('id') ? $request->input('id') : $id);

        return redirect()->route('courses.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $resources = $this->service()->listTrashed();

        return view('course::admin.trashed')->withResources($resources);
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
        $this->service()->restore($request->has('id') ? $request->input('id') : $id);

        return back();
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Course\Http\Requests\OwnedCourseRequest $request
     * @param  integer                                  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedCourseRequest $request, $id = null)
    {
        $this->service()->delete($request->has('id') ? $request->input('id') : $id);

        return back();
    }
}
