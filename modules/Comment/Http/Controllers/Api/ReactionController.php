<?php

namespace Comment\Http\Controllers\Api;

use Comment\Http\Requests\ReactionRequest;
use Comment\Models\Comment;
use Comment\Services\CommentServiceInterface;
use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class ReactionController extends ApiController
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
     * Like the given resource.
     *
     * @param  \Comment\Http\Requests\ReactionRequest $request
     * @param  \Comment\Models\Comment                $comment
     * @return \Illuminate\Http\Response
     */
    public function like(ReactionRequest $request, Comment $comment)
    {
        return response()->json($this->service->like($comment, $request->all()));
    }

    /**
     * Dislike the given resource.
     *
     * @param  \Comment\Http\Requests\ReactionRequest $request
     * @param  \Comment\Models\Comment                $comment
     * @return \Illuminate\Http\Response
     */
    public function dislike(ReactionRequest $request, Comment $comment)
    {
        return response()->json($this->service->dislike($comment, $request->all()));
    }
}
