<?php

namespace Menu\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Menu\Http\Requests\MenuRequest;
use Menu\Http\Resources\MenuResource;
use Menu\Services\MenuServiceInterface;

class SaveMenus extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Menu\Services\MenuServiceInterface $service
     * @param  \Menu\Http\Requests\MenuRequest     $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(MenuServiceInterface $service, MenuRequest $request)
    {
        return MenuResource::collection($service->save($request->all()));
    }
}
