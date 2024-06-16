<?php

namespace Announcement\Http\Controllers;

use Announcement\Http\Requests\AnnouncementRequest;
use Announcement\Http\Requests\OwnedAnnouncementRequest;
use Announcement\Models\Announcement;
use Announcement\Services\AnnouncementServiceInterface;
use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class AnnouncementController extends AdminController
{
    /**
     * Create a new controller instance.
     *
     * @param \Announcement\Services\AnnouncementServiceInterface $service
     */
    public function __construct(AnnouncementServiceInterface $service)
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

        return view('announcement::admin.index')->withResources($resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('announcement::admin.create')->withService($this->service());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Announcement\Http\Request\AnnouncementRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AnnouncementRequest $request)
    {
        $this->service()->store($request->all());

        return redirect()->route('announcements.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Announcement\Model\Announcement $announcement
     * @return \Illuminate\Http\Response
     */
    public function show(Announcement $announcement)
    {
        return view('announcement::admin.show')->withResource($announcement);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Announcement\Http\Requests\OwnedAnnouncementRequest $request
     * @param  \Announcement\Models\Announcement                    $announcement
     * @return \Illuminate\Http\Response
     */
    public function edit(OwnedAnnouncementRequest $request, Announcement $announcement)
    {
        return view('announcement::admin.edit')->withResource($announcement)->withService($this->service());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Announcement\Http\Requests\OwnedAnnouncementRequest $request
     * @param  \Announcement\Models\Announcement                    $announcement
     * @return \Illuminate\Http\Response
     */
    public function update(OwnedAnnouncementRequest $request, Announcement $announcement)
    {
        $this->service()->update($announcement->getKey(), $request->all());

        return redirect()->route('announcements.show', $announcement->getKey());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Announcement\Http\Requests\OwnedAnnouncementRequest $request
     * @param  integer                                              $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedAnnouncementRequest $request, $id = null)
    {
        $this->service()->destroy($request->has('id') ? $request->input('id') : $id);

        return redirect()->route('announcements.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $resources = $this->service()->listTrashed();

        return view('announcement::admin.trashed')->withResources($resources);
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Announcement\Http\Requests\OwnedAnnouncementRequest $request
     * @param  integer                                              $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedAnnouncementRequest $request, $id = null)
    {
        $this->service()->restore($request->has('id') ? $request->input('id') : $id);

        return back();
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Announcement\Http\Requests\OwnedAnnouncementRequest $request
     * @param  integer                                              $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedAnnouncementRequest $request, $id = null)
    {
        $this->service()->delete($request->has('id') ? $request->input('id') : $id);

        return back();
    }
}
