<?php

namespace User\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use User\Http\Resources\PermissionResource;
use User\Services\PermissionServiceInterface;

class ResetPermissionsList extends ApiController
{
    /**
     * Refresh the permissions list.
     *
     * @param  \Illuminate\Http\Request                  $request
     * @param  \User\Services\PermissionServiceInterface $service
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request, PermissionServiceInterface $service)
    {
        return response()->json($service->refresh());
    }

    /**
     * Reset the permissions list.
     *
     * @param  \Illuminate\Http\Request                  $request
     * @param  \User\Services\PermissionServiceInterface $service
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request, PermissionServiceInterface $service)
    {
        return response()->json($service->reset());
    }
}
