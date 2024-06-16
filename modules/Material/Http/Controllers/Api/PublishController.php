<?php

namespace Material\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Material\Http\Requests\OwnedMaterialRequest;
use Material\Models\Material;
use Material\Services\MaterialServiceInterface;

class PublishController extends ApiController
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
     * Publish the given resource.
     *
     * @param  \Material\Http\Requests\OwnedMaterialRequest $request
     * @param  \Material\Models\Material                    $material
     * @return \Illuminate\Http\Response
     */
    public function publish(OwnedMaterialRequest $request, Material $material)
    {
        return response()->json($this->service()->publish($material));
    }

    /**
     * Unpublish the given resource.
     *
     * @param  \Material\Http\Requests\OwnedMaterialRequest $request
     * @param  \Material\Models\Material                    $material
     * @return \Illuminate\Http\Response
     */
    public function unpublish(OwnedMaterialRequest $request, Material $material)
    {
        return response()->json(
            $this->service()->unpublish($material)
        );
    }

    /**
     * Draft the specified resource.
     *
     * @param  \Material\Http\Requests\OwnedMaterialRequest $request
     * @param  \Material\Models\Material                    $material
     * @return \Illuminate\Http\Response
     */
    public function draft(OwnedMaterialRequest $request, Material $material)
    {
        return response()->json($this->service()->draft($material));
    }

    /**
     * Expire the specified resource.
     *
     * @param  \Material\Http\Requests\OwnedMaterialRequest $request
     * @param  \Material\Models\Material                    $material
     * @return \Illuminate\Http\Response
     */
    public function expire(OwnedMaterialRequest $request, Material $material)
    {
        return response()->json($this->service()->expire($material));
    }
}
