<?php

namespace Page\Http\Controllers;

use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Page\Http\Requests\OwnedPageRequest;
use Page\Http\Requests\PageRequest;
use Page\Models\Page;
use Page\Services\PageServiceInterface;

class PageController extends AdminController
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
        $resources = $this->service()->list();

        return view('page::admin.index')->withResources($resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('page::admin.create')->withService($this->service());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\PageRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(PageRequest $request)
    {
        $this->service()->store($request->all());

        return redirect()->route('pages.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Page\Models\Page $page
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page)
    {
        return view('page::admin.show')->withResource($page);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Page\Http\Requests\OwnedPageRequest $request
     * @param  Page\Models\Page                    $page
     * @return \Illuminate\Http\Response
     */
    public function edit(OwnedPageRequest $request, Page $page)
    {
        return view('page::admin.edit')->withResource($page)->withService($this->service());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Page\Http\Requests\PageRequest $request
     * @param  Page\Models\Page               $page
     * @return \Illuminate\Http\Response
     */
    public function update(PageRequest $request, Page $page)
    {
        $this->service()->update($page->getKey(), $request->all());

        return redirect()->route('pages.show', $page->getKey());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Page\Http\Requests\OwnedPageRequest $request
     * @param  integer                             $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedPageRequest $request, $id = null)
    {
        $this->service()->destroy($request->has('id') ? $request->input('id') : $id);

        return redirect()->route('pages.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $resources = $this->service()->listTrashed();

        return view('page::admin.trashed')->withResources($resources);
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Page\Http\Requests\OwnedPageRequest $request
     * @param  integer                              $id
     * @return Illuminate\Http\Response
     */
    public function restore(OwnedPageRequest $request, $id = null)
    {
        $this->service()->restore($request->has('id') ? $request->input('id') : $id);

        return back();
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  Page\Http\Requests\OwnedPageRequest $request
     * @param  integer                             $id
     * @return Illuminate\Http\Response
     */
    public function delete(OwnedPageRequest $request, $id = null)
    {
        $this->service()->delete($request->has('id') ? $request->input('id') : $id);

        return back();
    }

    /**
     * Preview the specified page.
     *
     * @param  \Page\Http\Requests\OwnedPageRequest $request
     * @param  \Page\Models\Page                    $page
     * @return \Illuminate\Http\Response
     */
    public function preview(OwnedPageRequest $request, Page $page)
    {
        return view('page::admin.preview')->withResource($page);
    }
}
