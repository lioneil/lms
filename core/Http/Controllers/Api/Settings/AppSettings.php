<?php

namespace Core\Http\Controllers\Api\Settings;

use Core\Http\Controllers\Api\ApiController;
use Setting\Services\SettingServiceInterface;

class AppSettings extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Setting\Services\SettingServiceInterface $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(SettingServiceInterface $service)
    {
        return response()->json($service->containsKey('app'));
    }
}
