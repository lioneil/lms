<?php

namespace User\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use User\Http\Requests\DeleteUserRequest;
use User\Http\Requests\UserRequest;
use User\Http\Resources\User as UserResource;
use User\Services\UserServiceInterface;

class UserController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \User\Services\UserServiceInterface $service
     */
    public function __construct(UserServiceInterface $service)
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
        return UserResource::collection($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \User\Http\Requests\UserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        return new UserResource($this->service()->store($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return new UserResource($this->service()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \User\Http\Requests\UserRequest $request
     * @param  integer                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        return response()->json($this->service()->update($id, $request->all()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \User\Http\Requests\DeleteUserRequest $request
     * @param  integer                               $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteUserRequest $request, $id)
    {
        return $this->service()->destroy(
            $request->has('id') ? $request->input('id') : $id
        );
    }

    /**
     * Display a listing of the soft-deleted resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        return UserResource::collection($this->service()->listTrashed());
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  integer                  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id = null)
    {
        return $this->service()->restore(
            $request->has('id') ? $request->input('id') : $id
        );
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \User\Http\Requests\DeleteUserRequest $request
     * @param  integer|null                          $id
     * @return \Illuminate\Http\Response
     */
    public function delete(DeleteUserRequest $request, $id = null)
    {
        return $this->service()->delete(
            $request->has('id') ? $request->input('id') : $id
        );
    }
}
