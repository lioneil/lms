<?php

namespace Course\Http\Controllers\Owned;

use Core\Http\Controllers\Controller;
use Course\Models\Content;
use Course\Models\Course;

class ShowCourseContent extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Course\Models\Course  $course
     * @param  \Course\Models\Content $content
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Course $course, Content $content)
    {
        return view('course::public.content')->withResource($content);
    }
}
