<?php

namespace Course\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Http\Request;
use Subscription\Http\Requests\ProgressRequest;

class UpdateUserCourseProgress extends ApiController
{
    /**
     * Update the user's course progress.
     *
     * @param  \Subscription\Http\Requests\ProgressRequest $request
     * @param  \Course\Models\Course                       $course
     * @param  \Course\Services\CourseServiceInterface     $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ProgressRequest $request, Course $course, CourseServiceInterface $service)
    {
        return response()->json($service->progress($course, $request->all()));
    }
}
