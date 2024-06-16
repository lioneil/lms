<?php

namespace User\Http\Controllers;

use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use User\Http\Requests\UserRequest;
use User\Models\User;
use User\Services\UserServiceInterface;

class UserController extends AdminController
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
        $resources = $this->service()->list();

        return view('user::admin.index')->withResources($resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $service = $this->service();

        return view('user::admin.create')->withService($service);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \User\Http\Requests\UserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $this->service()->store($request->all());

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \User\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('user::admin.show')->withResource($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \User\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('user::admin.edit')->withResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \User\Http\Requests\UserRequest $request
     * @param  integer                         $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, int $id)
    {
        $this->service()->update($id, $request->all());

        return redirect()->route('users.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->service()->destroy($id);

        return back();
    }

    /**
     * Display a listing of the soft-deleted resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $resources = $this->service()->listTrashed();

        return view('user::admin.trashed')->withResources($resources);
    }
}
