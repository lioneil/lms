<?php

namespace Library\Http\Controllers;

use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Library\Http\Requests\LibraryRequest;
use Library\Http\Requests\OwnedLibraryRequest;
use Library\Models\Library;
use Library\Services\LibraryServiceInterface;

class LibraryController extends AdminController
{
    /**
     * Create a new controller instance.
     *
     * @param \Library\Services\LibraryServiceInterface $service
     */
    public function __construct(LibraryServiceInterface $service)
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

        return view('library::admin.index')->withResources($resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('library::admin.create')->withService($this->service());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Library\Htpp\Request\LibraryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(LibraryRequest $request)
    {
        $this->service()->store($request->all());

        return redirect()->route('libraries.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Library\Model\Library $library
     * @return \Illuminate\Http\Response
     */
    public function show(Library $library)
    {
        return view('library::admin.show')->withResource($library);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Library\Http\Requests\OwnedLibraryRequest $request
     * @param  \Library\Models\Library                    $library
     * @return \Illuminate\Http\Response
     */
    public function edit(OwnedLibraryRequest $request, Library $library)
    {
        return view('library::admin.edit')->withResource($library)->withService($this->service());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Library\Http\Requests\OwnedLibraryRequest $request
     * @param  \Library\Models\Library                    $library
     * @return \Illuminate\Http\Response
     */
    public function update(OwnedLibraryRequest $request, Library $library)
    {
        $this->service()->update($library->getKey(), $request->all());

        return redirect()->route('libraries.show', $library->getKey());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Library\Http\Requests\OwnedLibraryRequest $request
     * @param  integer                                    $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedLibraryRequest $request, $id = null)
    {
        $this->service()->destroy($request->has('id') ? $request->input('id') : $id);

        return redirect()->route('libraries.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $resources = $this->service()->listTrashed();

        return view('library::admin.trashed')->withResources($resources);
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Library\Http\Requests\OwnedLibraryRequest $request
     * @param  integer                                    $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedLibraryRequest $request, $id = null)
    {
        $this->service()->restore($request->has('id') ? $request->input('id') : $id);

        return back();
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Library\Http\Requests\OwnedLibraryRequest $request
     * @param  integer                                    $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedLibraryRequest $request, $id = null)
    {
        $this->service()->delete($request->has('id') ? $request->input('id') : $id);

        return back();
    }
}
