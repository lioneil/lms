<?php

namespace Announcement\Http\Controllers;

use Announcement\Http\Requests\AnnouncementRequest;
use Announcement\Services\AnnouncementService;
use Core\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AutoDelete extends Controller
{
    /**
     * The property on class instances.
     *
     * @param  \Announcement\Services\AnnouncementRequest $request
     * @param  \Announcement\Services\AnnouncementService $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(AnnouncementRequest $request, AnnouncementService $service)
    {
        return $service->autoclean($request->all());
    }
}
