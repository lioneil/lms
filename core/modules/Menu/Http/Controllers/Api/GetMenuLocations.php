<?php

namespace Menu\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Menu\Services\MenuServiceInterface;

class GetMenuLocations extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request            $request
     * @param  \Menu\Services\MenuServiceInterface $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, MenuServiceInterface $service)
    {
        return response()->json($service->listLocations($request->all()));
    }
}
