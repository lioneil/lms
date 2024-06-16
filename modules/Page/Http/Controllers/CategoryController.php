<?php

namespace Page\Http\Controllers;

use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Page\Http\Request\CategoryRequest;
use Page\Http\Request\OwnedCategoryRequest;
use Page\Services\CategoryServiceInterface;
use Taxonomy\Models\Category;

class CategoryController extends AdminController
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
        $resources = $this->service()->list();

        return view('page::categories.index')->withResources($resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('page::categories.create')->withService($this->service());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Page\Http\Request\CategoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $this->service()->store($request->all());

        return redirect()->route('categories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Taxonomy\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return view('page::categories.show')->withResource($category);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Page\Http\Request\OwnedCategoryRequest $request
     * @param  \Taxonomy\Models\Category               $category
     * @return \Illuminate\Http\Response
     */
    public function edit(OwnedCategoryRequest $request, Category $category)
    {
        return view('page::categories.edit')->withResource($category)->withService($this->service());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Page\Http\Request\OwnedCategoryRequest $request
     * @param  \Taxonomy\Models\Category               $category
     * @return \Illuminate\Http\Response
     */
    public function update(OwnedCategoryRequest $request, Category $category)
    {
        $this->service()->update($category->getKey(), $request->all());

        return redirect()->route('categories.show', $category->getKey());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Page\Http\Request\OwnedCategoryRequest $request
     * @param  integer                                 $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedCategoryRequest $request, $id = null)
    {
        $this->service()->destroy($request->has('id') ? $request->input('id') : $id);

        return redirect()->route('categories.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $resources = $this->service()->listTrashed();

        return view('page::categories.trashed')->withResources($resources);
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Page\Http\Request\OwnedCategoryRequest $request
     * @param  integer                                 $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedCategoryRequest $request, $id = null)
    {
        $this->service()->restore($request->has('id') ? $request->input('id') : $id);

        return back();
    }

    /**
     * Permanently delete the specifed resource.
     *
     * @param  \Page\Http\Request\OwnedCategoryRequest $request
     * @param  integer                                 $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedCategoryRequest $request, $id = null)
    {
        $this->service()->delete($request->has('id') ? $request->input('id') : $id);

        return back();
    }
}
