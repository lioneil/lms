<?php

namespace Material\Http\Controllers;

use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Material\Http\Requests\MaterialRequest;
use Material\Http\Requests\OwnedMaterialRequest;
use Material\Models\Material;
use Material\Services\MaterialServiceInterface;

class MaterialController extends AdminController
{
    /**
     * Create a new controller instance.
     *
     * @param \Material\Services\MaterialServiceInterface $service
     */
    public function __construct(MaterialServiceInterface $service)
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

        return view('material::admin.index')->withResources($resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('material::admin.create')->withService($this->service());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Material\Htpp\Request\MaterialRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(MaterialRequest $request)
    {
        $this->service()->store($request->all());

        return redirect()->route('materials.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Material\Model\Material $material
     * @return \Illuminate\Http\Response
     */
    public function show(Material $material)
    {
        return view('material::admin.show')->withResource($material);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Material\Http\Requests\OwnedMaterialRequest $request
     * @param  \Material\Models\Material                    $material
     * @return \Illuminate\Http\Response
     */
    public function edit(OwnedMaterialRequest $request, Material $material)
    {
        return view('material::admin.edit')->withResource($material)->withService($this->service());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Material\Http\Requests\OwnedMaterialRequest $request
     * @param  \Material\Models\Material                    $material
     * @return \Illuminate\Http\Response
     */
    public function update(OwnedMaterialRequest $request, Material $material)
    {
        $this->service()->update($material->getKey(), $request->all());

        return redirect()->route('materials.show', $material->getKey());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Material\Http\Requests\OwnedMaterialRequest $request
     * @param  integer                                      $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(OwnedMaterialRequest $request, $id = null)
    {
        $this->service()->destroy($request->has('id') ? $request->input('id') : $id);

        return redirect()->route('materials.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $resources = $this->service()->listTrashed();

        return view('material::admin.trashed')->withResources($resources);
    }

    /**
     * Restore the specified resource.
     *
     * @param  \Material\Http\Requests\OwnedMaterialRequest $request
     * @param  integer                                      $id
     * @return \Illuminate\Http\Response
     */
    public function restore(OwnedMaterialRequest $request, $id = null)
    {
        $this->service()->restore($request->has('id') ? $request->input('id') : $id);

        return back();
    }

    /**
     * Permanently delete the specified resource.
     *
     * @param  \Material\Http\Requests\OwnedMaterialRequest $request
     * @param  integer                                      $id
     * @return \Illuminate\Http\Response
     */
    public function delete(OwnedMaterialRequest $request, $id = null)
    {
        $this->service()->delete($request->has('id') ? $request->input('id') : $id);

        return back();
    }
}
