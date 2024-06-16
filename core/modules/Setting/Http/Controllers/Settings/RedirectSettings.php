<?php

namespace Setting\Http\Controllers\Settings;

use Core\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RedirectSettings extends Controller
{
    /**
     * The controller to render the redirected page.
     *
     * @var string
     */
    protected $redirect = '\Setting\Http\Controllers\Settings\ShowPreference';

    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return redirect()->action($this->redirect);
    }
}
