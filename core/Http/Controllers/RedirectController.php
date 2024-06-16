<?php

namespace Core\Http\Controllers;

use Core\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    /**
     * Redirect to home.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function home(Request $request)
    {
        return redirect()->route('home');
    }

    /**
     * Redirect to dashboard.
     *
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        return redirect()->route('dashboard');
    }
}
