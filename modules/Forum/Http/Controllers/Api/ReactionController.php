<?php

namespace Forum\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Forum\Http\Requests\ReactionRequest;
use Forum\Models\Thread;
use Forum\Services\ThreadServiceInterface;

class ReactionController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Forum\Services\ThreadServiceInterface $service
     */
    public function __construct(ThreadServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Like the given resource.
     *
     * @param  \Forum\Http\Requests\ReactionRequest $request
     * @param  \Forum\Models\Thread                 $thread
     * @return \Illuminate\Http\Response
     */
    public function like(ReactionRequest $request, Thread $thread)
    {
        return response()->json($this->service->like($thread, $request->all()));
    }

    /**
     * Dislike the given resource.
     *
     * @param  \Forum\Http\Requests\ReactionRequest $request
     * @param  \Forum\Models\Thread                 $thread
     * @return \Illuminate\Http\Response
     */
    public function dislike(ReactionRequest $request, Thread $thread)
    {
        return response()->json($this->service->dislike($thread, $request->all()));
    }
}
