<?php

namespace Comment\Http\Controllers;

use Comment\Http\Requests\CommentRequest;
use Comment\Http\Requests\OwnedCommentRequest;
use Comment\Models\Comment;
use Comment\Services\CommentServiceInterface;
use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class CommentController extends AdminController
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resources = $this->service()->list();

        return view('comment::admin.index')->withResources($resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('comment::admin.create')->withService($this->service());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Comment\Http\Requests\CommentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request)
    {
        $this->service()->store($request->all());

        return redirect()->route('comments.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Comment\Models\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        return view('comment::admin.show')->withResource($comment);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Comment\Http\Requests\OwnedCommentRequest $request
     * @param  \Comment\Models\Comment                    $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(OwnedCommentRequest $request, Comment $comment)
    {
        return view('comment::admin.edit')->withResource($comment)->withService($this->service());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Comment\Http\Requests\CommentRequest $request
     * @param  \Comment\Models\Comment               $comment
     * @return \Illuminate\Http\Response
     */
    public function update(CommentRequest $request, Comment $comment)
    {
        $this->service()->update($comment->getKey(), $request->all());

        return redirect()->route('comments.show', $comment->getKey());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Comment\Http\Requests\OwnedCommentRequest $request
     * @param  integer                                    $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedCommentRequest $request, $id = null)
    {
        $this->service()->destroy($request->has('id') ? $request->input('id') : $id);

        return redirect()->route('comments.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $resources = $this->service()->listTrashed();

        return view('comment::admin.trashed')->withRes;
        ($resources);
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Comment\Http\Requests\OwnedCommentRequest $request
     * @param  integer                                    $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedCommentRequest $request, $id = null)
    {
        $this->service()->restore($request->has('id') ? $request->input('id') : $id);

        return back();
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Comment\Http\Requests\OwnedCommentRequest $request
     * @param  integer                                    $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedCommentRequest $request, $id = null)
    {
        $this->service()->delete($request->has('id') ? $request->input('id') : $id);

        return back();
    }

    /**
     * Preview the specified comment.
     *
     * @param  \Comment\Http\Requests\OwnedCommentRequest $request
     * @param  \Comment\Models\Comment                    $comment
     * @return \Illuminate\Http\Response
     */
    public function preview(OwnedCommentRequest $request, Comment $comment)
    {
        return view('comment::admin.preview')->withResource($comment);
    }
}
