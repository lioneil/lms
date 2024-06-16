<?php

namespace Course\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Course\Http\Responses\ContentUploaded;
use Course\Services\ContentServiceInterface;
use Illuminate\Http\Request;

class UploadCourseContent extends ApiController
{
    const UPLOAD_SUCCESS = true;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request                 $request
     * @param  \Course\Services\ContentServiceInterface $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, ContentServiceInterface $service)
    {
        return response()->json([
            'uploaded' => self::UPLOAD_SUCCESS,
            'url' => $url = $service->upload($request->file('file')),
            'default' => $url,
        ]);
    }
}
