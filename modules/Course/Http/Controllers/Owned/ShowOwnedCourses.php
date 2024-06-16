<?php

namespace Course\Http\Controllers\Owned;

use Core\Http\Controllers\Controller;
use Course\Services\CourseServiceInterface;
use Illuminate\Http\Request;

class ShowOwnedCourses extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Course\Services\CourseServiceInterface $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(CourseServiceInterface $service)
    {
        return view('course::admin.owned')->withResources($service->list());
    }
}
