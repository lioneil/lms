<?php

namespace Widget\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Widget\Http\Requests\DashboardWidgetRequest;
use Widget\Http\Resources\WidgetResource;
use Widget\Services\WidgetServiceInterface;

class GetDashboardWidget extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Widget\Http\Requests\DashboardWidgetRequest $request
     * @param  \Widget\Services\WidgetServiceInterface      $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(DashboardWidgetRequest $request, WidgetServiceInterface $service)
    {
        return WidgetResource::collection($service->listByRole($request->all()));
    }
}
