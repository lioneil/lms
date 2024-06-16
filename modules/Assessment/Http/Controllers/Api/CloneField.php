<?php

namespace Assessment\Http\Controllers\Api;

use Assessment\Http\Requests\CloneFieldRequest;
use Assessment\Http\Resources\FieldResource;
use Assessment\Services\FieldServiceInterface;
use Core\Http\Controllers\Api\ApiController;

class CloneField extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Assessment\Http\Requests\CloneFieldRequest $request
     * @param  \Assessment\Services\FieldServiceInterface  $service
     * @param  integer                                     $id
     * @return \Illuminate\Http\Response
     */
    public function __invoke(CloneFieldRequest $request, FieldServiceInterface $service, $id)
    {
        return new FieldResource($service->clone($id, $request->all()));
    }
}
