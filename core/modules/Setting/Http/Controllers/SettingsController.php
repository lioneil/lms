<?php

namespace Setting\Http\Controllers;

use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Setting\Services\SettingServiceInterface;

class SettingsController extends AdminController
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = $this->service()->all();

        return view('setting::admin.index')->withSettings($settings);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->service()->store($request->except(['_token']));

        return back();
    }
}
