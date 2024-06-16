<?php

namespace Announcement\Http\Controllers;

use Announcement\Http\Requests\CategoryRequest;
use Announcement\Http\Requests\OwnedCategoryRequest;
use Announcement\Services\CategoryServiceInterface;
use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Taxonomy\Models\Category;

class CategoryController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @param \Category\Services\CategoryServiceInterface $service
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

        return view('announcement::categories.index')->withResources($resources);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('announcement::categories.create')->withService($this->service());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\CategoryRequest $request
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
     * @param  \Category\Model\Category $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return view('announcement::categories.show')->withResource($category);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Category\Http\Requests\OwnedCategoryRequest $request
     * @param  \Taxonomy\Models\Category                    $category
     * @return \Illuminate\Http\Response
     */
    public function edit(OwnedCategoryRequest $request, Category $category)
    {
        return view('announcement::categories.edit')->withResource($category)->withService($this->service());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Category\Http\Requests\OwnedCategoryRequest $request
     * @param  \Taxonomy\Models\Category                    $category
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
     * @param  \Category\Http\Requests\OwnedCategoryRequest $request
     * @param  integer                                      $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedCategoryRequest $request, $id = null)
    {
        $this->service()->destroy($request->has('id') ? $request->input('id') : $id);

        return redirect()->route('categories.index');
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Category\Http\Requests\OwnedCategoryRequest $request
     * @param  integer                                      $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedCategoryRequest $request, $id = null)
    {
        $this->service()->restore($request->has('id') ? $request->input('id') : $id);

        return back();
    }
}
