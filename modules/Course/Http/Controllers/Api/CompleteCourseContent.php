<?php

namespace Course\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Course\Http\Requests\CompletedContentRequest;
use Course\Http\Resources\ContentResource;
use Course\Models\Content;
use Course\Services\ContentServiceInterface;
use Illuminate\Http\Request;

class CompleteCourseContent extends ApiController
{
    /**
     * Complete the course content for the given user.
     *
     * @param  \Course\Http\Requests\CompletedContentRequest $request
     * @param  \Course\Models\Content                        $content
     * @param  \Course\Services\ContentServiceInterface      $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(CompletedContentRequest $request, Content $content, ContentServiceInterface $service)
    {
        return new ContentResource($service->complete($content, $request->all()));
    }
}
