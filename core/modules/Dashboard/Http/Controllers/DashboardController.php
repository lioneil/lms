<?php

namespace Dashboard\Http\Controllers;

use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class DashboardController extends AdminController
{
    /**
     * Show list of resources.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('dashboard::admin.index');
    }
}
