<?php

namespace Course\Http\Controllers\Web;

use Core\Http\Controllers\Controller;
use Course\Http\Requests\PublishedRequest;
use Course\Models\Course;
use Course\Services\CourseServiceInterface;
use Illuminate\Http\Request;

class CourseController extends Controller
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
        $resources = $service->listPublished();

        return view('course::public.all')->withResources($resources);
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
        return view('course::public.single')->withResource($course);
    }
}
