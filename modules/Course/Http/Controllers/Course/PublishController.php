<?php

namespace Course\Http\Controllers\Course;

use Core\Http\Controllers\AdminController;
use Course\Http\Requests\OwnedCourseRequest;
use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Http\Request;

class PublishController extends AdminController
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
     * Preview the specified course.
     *
     * @param  \Course\Http\Requests\OwnedCourseRequest $request
     * @param  \Course\Models\Course                    $course
     * @return \Illuminate\Http\Response
     */
    public function preview(OwnedCourseRequest $request, Course $course)
    {
        return view('course::admin.preview')->withResource($course);
    }

    /**
     * Publish the given resource.
     *
     * @param  \Course\Http\Requests\OwnedCourseRequest $request
     * @param  \Course\Models\Course                    $course
     * @return \Illuminate\Http\Response
     */
    public function publish(OwnedCourseRequest $request, Course $course)
    {
        $this->service()->findOrFail($course->getKey())->publish();

        return redirect()->route('courses.show', $course->getKey());
    }

    /**
     * Unpublish the given resource.
     *
     * @param  \Course\Http\Requests\OwnedCourseRequest $request
     * @param  \Course\Models\Course                    $course
     * @return \Illuminate\Http\Response
     */
    public function unpublish(OwnedCourseRequest $request, Course $course)
    {
        $this->service()->findOrFail($course->getKey())->unpublish();

        return redirect()->route('courses.show', $course->getKey());
    }

    /**
     * Draft the specified resource.
     *
     * @param  \Course\Http\Requests\OwnedCourseRequest $request
     * @param  \Course\Models\Course                    $course
     * @return \Illuminate\Http\Response
     */
    public function draft(OwnedCourseRequest $request, Course $course)
    {
        $this->service()->findOrFail($course->getKey())->draft();

        return redirect()->route('courses.show', $course->getKey());
    }
}
