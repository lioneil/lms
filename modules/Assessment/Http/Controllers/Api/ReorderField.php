<?php

namespace Assessment\Http\Controllers\Api;

use Assessment\Http\Requests\ReorderFieldRequest;
use Assessment\Http\Resources\FieldResource;
use Assessment\Services\FieldServiceInterface;
use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class ReorderField extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Assessment\Http\Requests\ReorderFieldRequest $request
     * @param  \Assessment\Services\FieldServiceInterface    $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ReorderFieldRequest $request, FieldServiceInterface $service)
    {
        return response()->json($service->reorder($request->all()));
    }
}
