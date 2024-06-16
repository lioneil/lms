<?php

namespace Widget\Http\Controllers\Widgets;

use Core\Application\Service\service;
use Core\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\refresh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Widget\Http\Controllers\Widgets\ShowWidgetPage;
use Widget\Services\WidgetService;
use Widget\Services\WidgetServiceInterface;

class ShowWidgetPage extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Widget\Services\WidgetServiceInterface  $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(WidgetServiceInterface $service)
    {
        return $service->refresh();
    }
}
