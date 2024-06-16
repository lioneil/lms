<?php

namespace User\Http\Controllers\Admin;

use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;

class RedirectProfile extends AdminController
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return redirect()->route('users.profile', user()->getUsername());
    }
}
