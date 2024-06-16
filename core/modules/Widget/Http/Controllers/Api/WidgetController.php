<?php

namespace Widget\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Widget\Http\Requests\WidgetRequest;
use Widget\Http\Resources\WidgetResource;
use Widget\Services\WidgetServiceInterface;

class WidgetController extends ApiController
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
        return WidgetResource::collection($this->service()->list());
    }

    /**
     * Display the specified resource.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return new WidgetResource($this->service()->find($id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Widget\Http\Requests\WidgetRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(WidgetRequest $request)
    {
        return response()->json($this->service()->store($request->all()));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Widget\Http\Requests\WidgetRequest $request
     * @param  integer                             $id
     * @return \Illuminate\Http\Response
     */
    public function update(WidgetRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }
}
