<?php

namespace Course\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Course\Http\Requests\OwnedCourseRequest;
use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Http\Request;

class PublishController extends ApiController
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
     * Publish the given resource.
     *
     * @param  \Course\Http\Requests\OwnedCourseRequest $request
     * @param  \Course\Models\Course                    $course
     * @return \Illuminate\Http\Response
     */
    public function publish(OwnedCourseRequest $request, Course $course)
    {
        return response()->json($this->service()->publish($course));
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
        return response()->json(
            $this->service()->findOrFail($course->getKey())->unpublish()
        );
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
        return response()->json($this->service()->draft($course));
    }

    /**
     * Expire the specified resource.
     *
     * @param  \Course\Http\Requests\OwnedCourseRequest $request
     * @param  \Course\Models\Course                    $course
     * @return \Illuminate\Http\Response
     */
    public function expire(OwnedCourseRequest $request, Course $course)
    {
        return response()->json($this->service()->expire($course));
    }
}
