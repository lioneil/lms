<?php

namespace Course\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Course\Http\Requests\PublishedRequest;
use Course\Http\Resources\ContentResource;
use Course\Models\Content;
use Course\Models\Course;
use Illuminate\Http\Request;

class GetCourseContent extends ApiController
{
    /**
     * Display the specified resource.
     *
     * @param  \Course\Http\Requests\PublishedRequest $request
     * @param  \Course\Models\Course                  $course
     * @param  \Course\Models\Content                 $content
     * @return \Illuminate\Http\Response
     */
    public function __invoke(PublishedRequest $request, Course $course, Content $content)
    {
        return new ContentResource($content);
    }
}
