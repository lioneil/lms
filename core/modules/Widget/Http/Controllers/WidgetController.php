<?php

namespace Widget\Http\Controllers;

use Core\Application\Widget\Factories\WidgetFactory;
use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Widget\Http\Requests\WidgetRequest;
use Widget\Services\WidgetServiceInterface;
use Widget\Models\Widget;

class WidgetController extends AdminController
{
    /**
     * Create a new controller instance.
     *
     * @param \Widget\Services\WidgetServiceInterface $service
     */
    public function __construct(WidgetServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resources = $this->service()->defaults();//->list();

        return view('widget::settings.index')->withResources($resources);
    }

    /**
      * Display the specified resource.
      *
      * @param  \Widget\Model\Widget $widget
      * @return \Illuminate\Http\Response
    */
    public function show(Widget $widget)
    {
        return view('widget::admin.show')->withResource($widget);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Widget\Htpp\Request\WidgetRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(WidgetRequest $request)
    {
        $this->service()->store($request->all());

        return redirect()->route('widgets.index');
    }
}
