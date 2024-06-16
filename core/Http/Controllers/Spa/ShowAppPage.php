<?php

namespace Core\Http\Controllers\Spa;

use Illuminate\Http\Request;
use Core\Http\Controllers\Controller;

class ShowAppPage extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return view('theme::app');
    }
}
