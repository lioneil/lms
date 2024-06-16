<?php

namespace Assessment\Http\Controllers;

use Assessment\Http\Requests\OwnedSubmissionRequest;
use Assessment\Http\Requests\SubmissionRequest;
use Assessment\Models\Submission;
use Assessment\Services\SubmissionServiceInterface;
use Core\Http\Controllers\AdminController;

class SubmissionController extends AdminController
{
    /**
     * Create a new controller instance.
     *
     * @param \Assessment\Services\SubmissionServiceInterface $service
     */
    public function __construct(SubmissionServiceInterface $service)
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

        return view('assessment::submission.index')->withResources($resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('assessment::submission.create')->withService($this->service());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Assessment\Http\Requests\SubmissionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubmissionRequest $request)
    {
        $this->service()->store($request->all());

        return redirect()->route('submissions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Assessment\Models\Submission $submission
     * @return \Illuminate\Http\Response
     */
    public function show(Submission $submission)
    {
        return view('assessment::submission.show')->withResource($submission);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Assessment\Http\Request\OwnedSubmissionRequest $request
     * @param  \Assessment\Models\Submission                   $submission
     * @return \Illuminate\Http\Response
     */
    public function edit(OwnedSubmissionRequest $request, Submission $submission)
    {
        return view('assessment::submission.edit')->withResource($submission)->withService($this->service());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Assessment\Http\Requests\SubmissionRequest $request
     * @param  \Assessment\Models\Submission               $submission
     * @return \Illuminate\Http\Response
     */
    public function update(SubmissionRequest $request, Submission $submission)
    {
        $this->service()->update($submission->getKey(), $request->all());

        return redirect()->route('submissions.show', $submission->getKey());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Assessment\Http\Request\OwnedSubmissionRequest $request
     * @param  integer                                         $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedSubmissionRequest $request, $id = null)
    {
        $this->service()->destroy($request->has('id') ? $request->input('id') : $id);

        return redirect()->route('submissions.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $resources = $this->service()->listTrashed();

        return view('assessment::submission.trashed')->withResources($resources);
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Assessment\Http\Request\OwnedSubmissionRequest $request
     * @param  integer                                         $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedSubmissionRequest $request, $id = null)
    {
        $this->service()->restore($request->has('id') ? $request->input('id') : $id);

        return back();
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Assessment\Http\Request\OwnedSubmissionRequest $request
     * @param  integer                                         $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedSubmissionRequest $request, $id = null)
    {
        $this->service()->delete($request->has('id') ? $request->input('id') : $id);

        return back();
    }
}
