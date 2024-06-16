<?php

namespace Core\Http\Controllers\Api;

use Core\Application\Service\WithService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ApiController extends Controller
{
    use WithService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('api');
    }

    /**
     * Send the data as JSON.
     *
     * @param  mixed $data
     * @return \Illuminate\Http\Response
     */
    public function toJSON($data = null)
    {
        return response()->json([
            $data,
        ]);
    }
}
