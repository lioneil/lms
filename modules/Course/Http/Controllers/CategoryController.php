<?php

namespace Course\Http\Controllers;

use Core\Http\Controllers\AdminController;
use Course\Http\Request\CategoryRequest;
use Course\Http\Request\OwnedCategoryRequest;
use Course\Services\CategoryServiceInterface;
use Illuminate\Http\Request;
use Taxonomy\Models\Category;

class CategoryController extends AdminController
{
    /**
     * Create a new controller instance.
     *
     * @param \Course\Services\CategoryServiceInterface $service
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

        return view('course::categories.index')->withResources($resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('course::categories.create')->withService($this->service());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Course\Http\Request\CategoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $this->service()->store($request->all());

        return redirect()->route('courses.categories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Taxonomy\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return view('course::categories.show')->withResource($category);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Course\Http\Request\OwnedCategoryRequest $request
     * @param  \Taxonomy\Models\Category                 $category
     * @return \Illuminate\Http\Response
     */
    public function edit(OwnedCategoryRequest $request, Category $category)
    {
        return view('course::categories.edit')->withResource($category)->withService($this->service());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Course\Http\Request\OwnedCategoryRequest $request
     * @param  \Taxonomy\Models\Category                 $category
     * @return \Illuminate\Http\Response
     */
    public function update(OwnedCategoryRequest $request, Category $category)
    {
        $this->service()->update($category->getKey(), $request->all());

        return redirect()->route('courses.categories.show', $category->getKey());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Course\Http\Request\OwnedCategoryRequest $request
     * @param  integer                                   $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedCategoryRequest $request, $id = null)
    {
        $this->service()->destroy($request->has('id') ? $request->input('id') : $id);

        return redirect()->route('courses.categories.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $resources = $this->service()->listTrashed();

        return view('course::categories.trashed')->withResources($resources);
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Course\Http\Request\OwnedCategoryRequest $request
     * @param  integer                                   $id
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
     * @param  \Course\Http\Request\OwnedCategoryRequest $request
     * @param  integer                                   $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedCategoryRequest $request, $id = null)
    {
        $this->service()->delete($request->has('id') ? $request->input('id') : $id);

        return back();
    }
}
