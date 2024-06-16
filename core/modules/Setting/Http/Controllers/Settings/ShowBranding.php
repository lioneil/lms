<?php

namespace Setting\Http\Controllers\Settings;

use Core\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShowBranding extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return view('setting::settings.branding');
    }
}
