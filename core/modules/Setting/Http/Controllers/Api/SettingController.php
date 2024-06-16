<?php

namespace Setting\Http\Controllers\Api;

use Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Setting\Services\SettingServiceInterface;

class SettingController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param \Setting\Services\SettingServiceInterface $service
     */
    public function __construct(SettingServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Retrieve the settings list from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json($this->service()->list());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        return response()->json($this->service()->store($request->all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        return response()->json($this->service()->upload(
            $request->all(), $request->input('key'), $request->input('name')
        ));
    }
}
