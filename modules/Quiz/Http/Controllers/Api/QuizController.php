<?php

namespace Quiz\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Quiz\Http\Controllers\Controller;
use Quiz\Http\Requests\OwnedQuizRequest;
use Quiz\Http\Requests\QuizRequest;
use Quiz\Http\Resources\QuizResource;
use Quiz\Services\QuizServiceInterface;

class QuizController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Quiz\Services\QuizServiceInterface $service
     */
    public function __construct(QuizServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request\OwnedQuizRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(OwnedQuizRequest $request)
    {
        return QuizResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\QuizRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuizRequest $request)
    {
        return response()->json($this->service()->store($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Quiz\Http\Requests\OwnedQuizRequest $request
     * @param  integer                              $id
     * @return \Illuminate\Http\Response
     */
    public function show(OwnedQuizRequest $request, int $id)
    {
        return new QuizResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\QuizRequest $request
     * @param  integer                      $id
     * @return \Illuminate\Http\Response
     */
    public function update(QuizRequest $request, int $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Quiz\Http\Requests\OwnedQuizRequest $request
     * @param  integer                              $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedQuizRequest $request, $id)
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
        return QuizResource::collection($this->service()->listTrashed());
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Quiz\Http\Requests\OwnedQuizRequest $request
     * @param  integer|null                         $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedQuizRequest $request, $id = null)
    {
        return $this->service()->restore(
            $request->has('id') ? $request->input('id') : $id
        );
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Quiz\Http\Requests\OwnedQuizRequest $request
     * @param  integer|null                         $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedQuizRequest $request, $id = null)
    {
        return $this->service()->delete(
            $request->has('id') ? $request->input('id') : $id
        );
    }
}
