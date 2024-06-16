<?php

namespace User\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use User\Http\Requests\RoleRequest;
use User\Http\Resources\RoleResource;
use User\Services\RoleServiceInterface;

class RoleController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Role\Services\RoleServiceInterface $service
     */
    public function __construct(RoleServiceInterface $service)
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
        return RoleResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \User\Http\Requests\RoleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        return response()->json($this->service()->store($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new RoleResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \User\Http\Requests\RoleRequest $request
     * @param  integer                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @param  integer                 $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        return $this->service()->destroy(
            $request->has('id') ? $request->input('id') : $id
        );
    }

    /**
     * Display a listing of the soft-deleted resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        return RoleResource::collection($this->service()->listTrashed());
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  integer                  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id = null)
    {
        return $this->service()->restore(
            $request->has('id') ? $request->input('id') : $id
        );
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  integer|null             $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id = null)
    {
        return $this->service()->delete(
            $request->has('id') ? $request->input('id') : $id
        );
    }
}
