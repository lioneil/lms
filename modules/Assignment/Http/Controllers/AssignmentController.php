<?php

namespace Assignment\Http\Controllers;

use Assignment\Http\Requests\AssignmentRequest;
use Assignment\Http\Requests\OwnedAssignmentRequest;
use Assignment\Models\Assignment;
use Assignment\Services\AssignmentServiceInterface;
use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class AssignmentController extends AdminController
{
    /**
     * Create a new controller instance.
     *
     * @param \Assignment\Services\AssignmentServiceInterface $service
     */
    public function __construct(AssignmentServiceInterface $service)
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

        return view('assignment::admin.index')->withResources($resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('assignment::admin.create')->withService($this->service());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\AssignmentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AssignmentRequest $request)
    {
        $this->service()->store($request->all());

        return redirect()->route('assignments.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Assignment\Models\Assignment $assignment
     * @return \Illuminate\Http\Response
     */
    public function show(Assignment $assignment)
    {
        return view('assignment::admin.show')->withResource($assignment);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Assignment\Http\Requests\OwnedAssignmentRequest $request
     * @param  Assignment\Models\Assignment                    $assignment
     * @return \Illuminate\Http\Response
     */
    public function edit(OwnedAssignmentRequest $request, Assignment $assignment)
    {
        return view('assignment::admin.edit')->withResource($assignment)->withService($this->service());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Assignment\Http\Requests\AssignmentRequest $request
     * @param  Assignment\Models\Assignment               $assignment
     * @return \Illuminate\Http\Response
     */
    public function update(AssignmentRequest $request, Assignment $assignment)
    {
        $this->service()->update($assignment->getKey(), $request->all());

        return redirect()->route('assignments.show', $assignment->getKey());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Assignment\Http\Requests\OwnedAssignmentRequest $request
     * @param  integer                                          $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedAssignmentRequest $request, $id = null)
    {
        $this->service()->destroy($request->has('id') ? $request->input('id') : $id);

        return redirect()->route('assignments.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $resources = $this->service()->listTrashed();

        return view('assignment::admin.trashed')->withResources($resources);
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Assignment\Http\Requests\OwnedAssignmentRequest $request
     * @param  integer                                          $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedAssignmentRequest $request, $id = null)
    {
        $this->service()->restore($request->has('id') ? $request->input('id') : $id);

        return back();
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Assignment\Http\Requests\OwnedAssignmentRequest $request
     * @param  integer                                          $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedAssignmentRequest $request, $id = null)
    {
        $this->service()->delete($request->has('id') ? $request->input('id') : $id);

        return back();
    }
}
