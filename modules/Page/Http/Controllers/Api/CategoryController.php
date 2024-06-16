<?php

namespace Page\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Page\Http\Request\CategoryRequest;
use Page\Http\Request\OwnedCategoryRequest;
use Page\Http\Resources\CategoryResource;
use Page\Services\CategoryServiceInterface;

class CategoryController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Page\Services\CategoryServiceInterface $service
     */
    public function __construct(CategoryServiceInterface $service)
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
        return CategoryResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Page\Http\Requests\CategoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
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
        return new CategoryResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Page\Http\Requests\CategoryRequest $request
     * @param  integer                             $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Page\Http\Requests\OwnedCategoryRequest $request
     * @param  integer                                  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedCategoryRequest $request, $id = null)
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
        return CategoryResource::collection($this->service()->listTrashed());
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Page\Http\Requests\OwnedCategoryRequest $request
     * @param  integer                                  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedCategoryRequest $request, $id = null)
    {
        return $this->service()->restore($request->has('id') ? $request->input('id') : $id);
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Page\Http\Requests\OwnedCategoryRequest $request
     * @param  integer|null                             $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedCategoryRequest $request, $id = null)
    {
        return $this->service()->delete($request->has('id') ? $request->input('id') : $id);
    }
}
