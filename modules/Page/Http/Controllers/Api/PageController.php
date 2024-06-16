<?php

namespace Page\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Page\Http\Requests\OwnedPageRequest;
use Page\Http\Requests\PageRequest;
use Page\Http\Resources\PageResource;
use Page\Services\PageServiceInterface;

class PageController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Page\Services\PageServiceInterface $service
     */
    public function __construct(PageServiceInterface $service)
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
        return PageResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Page\Http\Requests\PageRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PageRequest $request)
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
        return new PageResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Page\Http\Requests\PageRequest $request
     * @param  integer                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(PageRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Page\Http\Requests\OwnedPageRequest $request
     * @param  integer                              $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedPageRequest $request, $id = null)
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
        return PageResource::collection($this->service()->listTrashed());
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Page\Http\Requests\OwnedPageRequest $request
     * @param  integer                              $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedPageRequest $request, $id = null)
    {
        return $this->service()->restore($request->has('id') ? $request->input('id') : $id);
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Page\Http\Requests\OwnedPageRequest $request
     * @param  integer|null                         $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedPageRequest $request, $id = null)
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
        return PageResource::collection($this->service()->list());
    }
}
