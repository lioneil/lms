<?php

namespace Setting\Http\Controllers;

use Illuminate\Http\Request;

class SettingsProfile extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, SettingServiceInterface $service)
    {
        return view('setting::admin.index')->withResources($service->list());
    }
}
