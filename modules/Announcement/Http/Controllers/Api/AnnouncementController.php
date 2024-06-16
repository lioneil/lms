<?php

namespace Announcement\Http\Controllers\Api;

use Announcement\Http\Requests\AnnouncementRequest;
use Announcement\Http\Requests\OwnedAnnouncementRequest;
use Announcement\Http\Resources\AnnouncementResource;
use Announcement\Services\AnnouncementServiceInterface;
use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class AnnouncementController extends ApiController
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
     * @param  \Announcement\Http\Requests\OwnedAnnouncementRequest $request
     * @return \Illuminate\Http\Response
     */
    public function index(OwnedAnnouncementRequest $request)
    {
        return AnnouncementResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Announcement\Http\Requests\AnnouncementRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AnnouncementRequest $request)
    {
        return response()->json($this->service()->store($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Announcement\Http\Requests\OwnedAnnouncementRequest $request
     * @param  integer                                              $id
     * @return \Illuminate\Http\Response
     */
    public function show(OwnedAnnouncementRequest $request, int $id)
    {
        return new AnnouncementResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Announcement\Http\Requests\AnnouncementRequest $request
     * @param  integer                                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(AnnouncementRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Announcement\Http\Requests\OwnedAnnouncementRequest $request
     * @param  integer                                              $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedAnnouncementRequest $request, $id)
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
        return AnnouncementResource::collection($this->service()->listTrashed());
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Announcement\Http\Requests\OwnedAnnouncementRequest $request
     * @param  integer|null                                         $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedAnnouncementRequest $request, $id = null)
    {
        return $this->service()->restore($request->has('id') ? $request->input('id') : $id);
    }

    /**
     * Permanently delete the sepcified resource.
     *
     * @param  \Announcement\Http\Requests\OwnedAnnouncementRequest $request
     * @param  integer|null                                         $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedAnnouncementRequest $request, $id = null)
    {
        return $this->service()->delete($request->has('id') ? $request->input('id') : $id);
    }
}
