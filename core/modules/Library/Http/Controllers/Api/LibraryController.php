<?php

namespace Library\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Library\Http\Requests\LibraryRequest;
use Library\Http\Requests\OwnedLibraryRequest;
use Library\Http\Resources\LibraryResource;
use Library\Services\LibraryServiceInterface;

class LibraryController extends ApiController
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
     * @param  \Library\Http\Requests\OwnedLibraryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(OwnedLibraryRequest $request)
    {
        return LibraryResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Library\Http\Requests\LibraryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(LibraryRequest $request)
    {
        return response()->json($this->service()->store($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Library\Http\Requests\OwnedLibraryRequest $request
     * @param  integer                                    $id
     * @return \Illuminate\Http\Response
     */
    public function show(OwnedLibraryRequest $request, int $id)
    {
        return new LibraryResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Library\Http\Requests\LibraryRequest $request
     * @param  integer                               $id
     * @return \Illuminate\Http\Response
     */
    public function update(LibraryRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Library\Http\Requests\OwnedLibraryRequest $request
     * @param  integer                                    $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedLibraryRequest $request, $id)
    {
        return response()->json($this->service()->destroy(
            $request->has('id') ? $request->input('id') : $id
        ));
    }

    /**
     * Display a listing of the soft-deleted resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        return LibraryResource::collection($this->service()->listTrashed());
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Library\Http\Requests\OwnedLibraryRequest $request
     * @param  integer|null                               $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedLibraryRequest $request, $id = null)
    {
        return $this->service()->restore($request->has('id') ? $request->input('id') : $id);
    }

    /**
     * Permanently delete the sepcified resource.
     *
     * @param  \Library\Http\Requests\OwnedLibraryRequest $request
     * @param  integer|null                               $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedLibraryRequest $request, $id = null)
    {
        return $this->service()->delete($request->has('id') ? $request->input('id') : $id);
    }
}
