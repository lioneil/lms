<?php

namespace Taxonomy\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Taxonomy\Http\Requests\TagRequest;
use Taxonomy\Http\Resources\TagResource;
use Taxonomy\Services\TagServiceInterface;

class TagController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Taxonomy\Services\TagServiceInterface $service
     */
    public function __construct(TagServiceInterface $service)
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
        return TagResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Taxonomy\Http\Requests\TagRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(TagRequest $request)
    {
        return new TagResource($this->service()->store($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return new TagResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Taxonomy\Http\Requests\TagRequest $request
     * @param  integer                            $id
     * @return \Illuminate\Http\Response
     */
    public function update(TagRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  integer                  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id = null)
    {
        return $this->service()->destroy(
            $request->has('id') ? $request->input('id') : $id
        );
    }
}
