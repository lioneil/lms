<?php

namespace User\Http\Controllers;

use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use User\Models\Role;
use User\Services\RoleServiceInterface;

class RoleController extends AdminController
{
    /**
     * Create a new controller instance.
     *
     * @param \User\Services\RoleServiceInterface $service
     */
    public function __construct(RoleServiceInterface $service)
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
        $service = $this->service();
        $resources = $this->service()->list();

        return view('user::roles.index')->withResources($resources)->withService($service);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user::roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \User\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return view('user::roles.show')->withResource($role);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $service = $this->service();
        $resource = $service->find($id);

        return view('user::roles.edit')->withService($service)->withResource($resource);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  integer                  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    /**
     * Import the specified resource to storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $this->service()->import($request->all());

        return redirect()->route('roles.index');
    }
}
