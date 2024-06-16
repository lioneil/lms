<?php

namespace Core\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Core\Http\Requests\StorageRequest;
use Core\Services\FileServiceInterface;
use Illuminate\Http\Request;

class UploadFile extends ApiController
{
    const UPLOAD_SUCCESS = true;

    /**
     * Handle the incoming request.
     *
     * @param  \Core\Http\Requests\StorageRequest  $request
     * @param  \Core\Services\FileServiceInterface $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(StorageRequest $request, FileServiceInterface $service)
    {
        return response()->json([
            'uploaded' => self::UPLOAD_SUCCESS,
            'url' => $url = $service->upload($request->file('file')),
            'default' => $url,
        ]);
    }
}
