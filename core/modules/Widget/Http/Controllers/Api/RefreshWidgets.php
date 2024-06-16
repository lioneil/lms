<?php

namespace Widget\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Widget\Services\WidgetServiceInterface;

class RefreshWidgets extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Widget\Services\WidgetServiceInterface $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(WidgetServiceInterface $service)
    {
        return response()->json($service->refresh());
    }
}
