<?php

namespace Forum\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Forum\Http\Requests\OwnedThreadRequest;
use Forum\Http\Requests\ThreadRequest;
use Forum\Http\Resources\ThreadResource;
use Forum\Services\ThreadServiceInterface;

class ThreadController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Forum\Services\ThreadServiceInterface $service
     */
    public function __construct(ThreadServiceInterface $service)
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
        return ThreadResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Forum\Http\Requests\ThreadRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ThreadRequest $request)
    {
        return $this->service()->store($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return new ThreadResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Forum\Http\Requests\ThreadRequest $request
     * @param  integer                            $id
     * @return \Illuminate\Http\Response
     */
    public function update(ThreadRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Forum\Http\Requests\OwnedThreadRequest $request
     * @param  integer                                 $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedThreadRequest $request, $id = null)
    {
        return $this->service()->destroy($request->has('id') ? $request->input('id') : $id);
    }

    /**
     * Display a listing of the soft-deleted resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        return ThreadResource::collection($this->service()->listTrashed());
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Forum\Http\Requests\OwnedThreadRequest $request
     * @param  intger|null                             $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedThreadRequest $request, $id = null)
    {
        return $this->service()->restore($request->has('id') ? $request->input('id') : $id);
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Forum\Http\Requests\OwnedThreadRequest $request
     * @param  integer|null                            $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedThreadRequest $request, $id = null)
    {
        return $this->service()->delete($request->has('id') ? $request->input('id') : $id);
    }

    /**
     * Display a listing of the owned resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function owned()
    {
        return ThreadResource::collection($this->service()->list());
    }
}
