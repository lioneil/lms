<?php

namespace Course\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Course\Http\Requests\ContentRequest;
use Course\Http\Requests\OwnedContentRequest;
use Course\Http\Resources\ContentResource;
use Course\Services\ContentServiceInterface;

class ContentController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Course\Services\ContentServiceInterface $service
     */
    public function __construct(ContentServiceInterface $service)
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
        return ContentResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Course\Http\Requests\ContentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContentRequest $request)
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
        return new ContentResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Course\Http\Requests\ContentRequest $request
     * @param  integer                              $id
     * @return \Illuminate\Http\Response
     */
    public function update(ContentRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Course\Http\Requests\OwnedContentRequest $request
     * @param  integer                                   $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedContentRequest $request, $id = null)
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
        return ContentResource::collection($this->service()->listTrashed());
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Course\Http\Requests\OwnedContentRequest $request
     * @param  integer                                   $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedContentRequest $request, $id = null)
    {
        return $this->service()->restore($request->has('id') ? $request->input('id') : $id);
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Course\Http\Requests\OwnedContentRequest $request
     * @param  integer|null                              $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedContentRequest $request, $id = null)
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
        return ContentResource::collection($this->service()->list());
    }
}
