<?php

namespace Core\Http\Controllers\Api\Search;

use Core\Http\Controllers\Api\ApiController;
use Core\Repositories\SearchRepository;
use Illuminate\Http\Request;

class ShowSearchResults extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request            $request
     * @param  \Core\Repositories\SearchRepository $service
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, SearchRepository $service)
    {
        return response()->json($service->search($request->input('search'))->all());
    }
}
