<?php

namespace Assessment\Http\Controllers\Api;

use Assessment\Http\Requests\FieldRequest;
use Assessment\Http\Requests\OwnedFieldRequest;
use Assessment\Http\Resources\FieldResource;
use Assessment\Services\FieldServiceInterface;
use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class FieldController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Assessment\Services\FieldServiceInterface $service
     */
    public function __construct(FieldServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Assessment\Http\Requests\OwnedFieldRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(OwnedFieldRequest $request)
    {
        return FieldResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Assessment\Http\Requests\FieldRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(FieldRequest $request)
    {
        return response()->json($this->service()->store($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Assessment\Http\Requests\OwnedFieldRequest $request
     * @param  integer                                     $id
     * @return \Illuminate\Http\Response
     */
    public function show(OwnedFieldRequest $request, int $id)
    {
        return new FieldResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Assessment\Http\Requests\OwnedFieldRequest $request
     * @param  integer                                     $id
     * @return \Illuminate\Http\Response
     */
    public function update(FieldRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Assessment\Http\Requests\OwnedFieldRequest $request
     * @param  integer                                     $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedFieldRequest $request, $id = null)
    {
        return $this->service()->destroy($request->has('id') ? $request->input('id') : $id);
    }
}
