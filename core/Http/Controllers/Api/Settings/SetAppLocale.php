<?php

namespace Core\Http\Controllers\Api\Settings;

use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class SetAppLocale extends ApiController
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return response()->json(app()->setLocale($request->input('locale')));
    }
}
