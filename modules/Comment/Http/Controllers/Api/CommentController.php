<?php

namespace Comment\Http\Controllers\Api;

use Comment\Http\Requests\CommentRequest;
use Comment\Http\Requests\OwnedCommentRequest;
use Comment\Http\Resources\CommentResource;
use Comment\Services\CommentServiceInterface;
use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class CommentController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Comment\Services\CommentServiceInterface $service
     */
    public function __construct(CommentServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request\OwnedCommentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(OwnedCommentRequest $request)
    {
        return CommentResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\CommentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request)
    {
        return response()->json($this->service()->store($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Comment\Http\Requests\OwnedCommentRequest $request
     * @param  integer                                    $id
     * @return \Illuminate\Http\Response
     */
    public function show(OwnedCommentRequest $request, int $id)
    {
        return new CommentResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Comment\Http\Requests\CommentRequest $request
     * @param  integer                               $id
     * @return \Illuminate\Http\Response
     */
    public function update(CommentRequest $request, int $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Comment\Http\Requests\OwnedCommentRequest $request
     * @param  integer                                    $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedCommentRequest $request, $id)
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
        return CommentResource::collection($this->service()->listTrashed());
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Comment\Http\Requests\OwnedCommentRequest $request
     * @param  integer|null                               $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedCommentRequest $request, $id = null)
    {
        return $this->service()->restore(
            $request->has('id') ? $request->input('id') : $id
        );
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Comment\Http\Requests\OwnedCommentRequest $request
     * @param  integer|null                               $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedCommentRequest $request, $id = null)
    {
        return $this->service()->delete(
            $request->has('id') ? $request->input('id') : $id
        );
    }
}
