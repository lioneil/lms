<?php

namespace Quiz\Http\Controllers;

use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Quiz\Http\Requests\OwnedQuizRequest;
use Quiz\Http\Requests\QuizRequest;
use Quiz\Models\Form;
use Quiz\Models\Quiz;
use Quiz\Services\QuizServiceInterface;

class QuizController extends AdminController
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resources = $this->service()->list();

        return view('quiz::admin.index')->withResources($resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('quiz::admin.create')->withService($this->service());
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Quiz\Htpp\Request\QuizRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuizRequest $request)
    {
        $this->service()->store($request->all());

        return redirect()->route('quizzes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Quiz\Models\Quiz $quiz
     * @return \Illuminate\Http\Response
     */
    public function show(Quiz $quiz)
    {
        return view('quiz::admin.show')->withResource($quiz);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Quiz\Http\Requests\OwnedQuizRequest $request
     * @param  \Quiz\Models\Quiz                    $quiz
     * @return \Illuminate\Http\Response
     */
    public function edit(OwnedQuizRequest $request, Quiz $quiz)
    {
        return view('quiz::admin.edit')->withResource($quiz)->withService($this->service());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Quiz\Http\Requests\OwnedQuizRequest $request
     * @param  \Quiz\Models\Quiz                    $quiz
     * @return \Illuminate\Http\Response
     */
    public function update(OwnedQuizRequest $request, Quiz $quiz)
    {
        $this->service()->update($quiz->getKey(), $request->all());

        return redirect()->route('quizzes.show', $quiz->getKey());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Quiz\Http\Requests\OwnedQuizRequest $request
     * @param  integer                              $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedQuizRequest $request, $id = null)
    {
        $this->service()->destroy($request->has('id') ? $request->input('id') : $id);

        return redirect()->route('quizzes.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $resources = $this->service()->listTrashed();

        return view('quiz::admin.trashed')->withResources($resources);
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Quiz\Http\Requests\OwnedQuizRequest $request
     * @param  integer                              $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedQuizRequest $request, $id = null)
    {
        $this->service()->restore($request->has('id') ? $request->input('id') : $id);

        return back();
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Quiz\Http\Requests\OwnedQuizRequest $request
     * @param  integer                              $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedQuizRequest $request, $id = null)
    {
        $this->service()->delete($request->has('id') ? $request->input('id') : $id);

        return back();
    }
}
