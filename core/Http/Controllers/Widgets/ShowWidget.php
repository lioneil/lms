<?php

namespace Core\Http\Controllers\Widgets;

use Core\Application\Widget\Factories\WidgetFactory;
use Core\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShowWidget extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request                         $request
     * @param  \Core\Application\Widget\Factories\WidgetFactory $factory
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, WidgetFactory $factory)
    {
        return $factory->make($request->input('alias'));
    }
}
