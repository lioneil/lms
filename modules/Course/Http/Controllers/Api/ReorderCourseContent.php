<?php

namespace Course\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Course\Http\Requests\ReorderCourseContentRequest;
use Course\Http\Resources\ContentResource;
use Course\Services\ContentServiceInterface;
use Illuminate\Http\Request;

class ReorderCourseContent extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Course\Http\Requests\ReorderCourseContentRequest $request
     * @param  \Course\Services\ContentServiceInterface          $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ReorderCourseContentRequest $request, ContentServiceInterface $service)
    {
        return response()->json($service->reorder($request->all()));
    }
}
