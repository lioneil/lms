<?php

namespace Forum\Http\Controllers;

use Core\Http\Controllers\AdminController;
use Forum\Http\Requests\OwnedThreadRequest;
use Forum\Http\Requests\ThreadRequest;
use Forum\Models\Thread;
use Forum\Services\ThreadServiceInterface;

class ThreadController extends AdminController
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resources = $this->service()->list();

        return view('forum::admin.index')->withResources($resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('forum::admin.create')->withService($this->service());
    }

    /**
     * Store a newly creted resource in storage.
     *
     * @param  \Forum\Http\Requests\ThreadRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ThreadRequest $request)
    {
        $this->service()->store($request->all());

        return redirect()->route('threads.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Forum\Models\Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function show(Thread $thread)
    {
        return view('forum::admin.show')->withResource($thread);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Forum\Http\Requests\OwnedThreadRequest $request
     * @param  \Forum\Models\Thread                    $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(OwnedThreadRequest $request, Thread $thread)
    {
        return view('forum::admin.edit')->withResource($thread)->withService($this->service());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Forum\Http\Requests\ThreadRequest $request
     * @param  \Forum\Models\Thread               $thread
     * @return \Illuminate\Http\Response
     */
    public function update(ThreadRequest $request, Thread $thread)
    {
        $this->service()->update($thread->getKey(), $request->all());

        return redirect()->route('threads.show', $thread->getKey());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Forum\Http\Requests\OwnedThreadRequest $request
     * @param  integer                                 $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedThreadRequest $request, $id = null)
    {
        $this->service()->destroy($request->has('id') ? $request->input('id') : $id);

        return redirect()->route('threads.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $resources = $this->service()->listTrashed();

        return view('forum::admin.trashed')->withResources($resources);
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Forum\Http\Requests\OwnedThreadRequest $request
     * @param  integer                                 $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedThreadRequest $request, $id = null)
    {
        $this->service()->restore($request->has('id') ? $request->input('id') : $id);

        return back();
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Forum\Http\Requests\OwnedThreadRequest $request
     * @param  integer                                 $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedThreadRequest $request, $id = null)
    {
        $this->service()->delete($request->has('id') ? $request->input('id') : $id);

        return back();
    }

    /**
     * Preview the specified forum.
     *
     * @param  \Forum\Http\Requests\OwnedThreadRequest $request
     * @param  \Forum\Models\Thread                    $thread
     * @return \Illuminate\Http\Response
     */
    public function preview(OwnedThreadRequest $request, Thread $thread)
    {
        return view('forum::admin.preview')->withResource($thread);
    }
}
