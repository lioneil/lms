<?php

namespace Course\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Course\Http\Requests\PublishedRequest;
use Course\Http\Resources\WebCourseResource;
use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Http\Request;

class WebCourseController extends ApiController
{
    /**
     * Display list of all published resources.
     *
     * @param  \Illuminate\Http\Request                $request
     * @param  \Course\Services\CourseServiceInterface $service
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request, CourseServiceInterface $service)
    {
        return WebCourseResource::collection($service->listPublished());
    }

    /**
     * Display the specified resource.
     *
     * @param  \Course\Http\Requests\PublishedRequest $request
     * @param  \Course\Models\Course                  $course
     * @return \Illuminate\Http\Response
     */
    public function single(PublishedRequest $request, Course $course)
    {
        return new WebCourseResource($course);
    }
}
