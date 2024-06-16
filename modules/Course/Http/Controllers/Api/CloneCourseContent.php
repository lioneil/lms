<?php

namespace Course\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Course\Http\Resources\ContentResource;
use Course\Http\Requests\CloneCourseContentRequest;
use Course\Services\ContentServiceInterface;

class CloneCourseContent extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Course\Http\Requests\CloneCourseContentRequest $request
     * @param  \Course\Services\ContentServiceInterface        $service
     * @param  integer                                         $id
     * @return \Illuminate\Http\Response
     */
    public function __invoke(CloneCourseContentRequest $request, ContentServiceInterface $service, $id)
    {
        return new ContentResource($service->clone($id, $request->all()));
    }
}
