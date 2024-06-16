<?php

namespace User\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use User\Http\Resources\PermissionResource;
use User\Services\PermissionServiceInterface;

class GetPermissionsList extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request                  $request
     * @param  \User\Services\PermissionServiceInterface $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, PermissionServiceInterface $service)
    {
        return response()->json($service->grouped()->values());
    }
}
