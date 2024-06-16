<?php

namespace Material\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Material\Http\Controllers\Controller;
use Material\Http\Requests\MaterialRequest;
use Material\Http\Requests\OwnedMaterialRequest;
use Material\Http\Resources\MaterialResource;
use Material\Services\MaterialServiceInterface;

class MaterialController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Material\Services\MaterialServiceInterface $service
     */
    public function __construct(MaterialServiceInterface $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request\OwnedMaterialRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(OwnedMaterialRequest $request)
    {
        return MaterialResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\MaterialRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(MaterialRequest $request)
    {
        return response()->json($this->service()->store($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Material\Http\Requests\OwnedMaterialRequest $request
     * @param  integer                                      $id
     * @return \Illuminate\Http\Response
     */
    public function show(OwnedMaterialRequest $request, int $id)
    {
        return new MaterialResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\MaterialRequest $request
     * @param  integer                          $id
     * @return \Illuminate\Http\Response
     */
    public function update(MaterialRequest $request, int $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Material\Http\Requests\OwnedMaterialRequest $request
     * @param  integer                                      $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedMaterialRequest $request, $id)
    {
        return response()->json($this->service()->destroy(
            $request->has('id') ? $request->input('id') : $id
        ));
    }

    /**
     * Display a listing of the soft-deleted resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        return MaterialResource::collection($this->service()->listTrashed());
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Material\Http\Requests\OwnedMaterialRequest $request
     * @param  integer|null                                 $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedMaterialRequest $request, $id = null)
    {
        return $this->service()->restore(
            $request->has('id') ? $request->input('id') : $id
        );
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Material\Http\Requests\OwnedMaterialRequest $request
     * @param  integer|null                                 $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedMaterialRequest $request, $id = null)
    {
        return $this->service()->delete(
            $request->has('id') ? $request->input('id') : $id
        );
    }
}
