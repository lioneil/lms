<?php

namespace User\Http\Controllers\Admin;

use Core\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use User\Models\User;

class ShowProfile extends AdminController
{
    /**
     * Handle the incoming request.
     *
     * @param  \User\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function __invoke(User $user)
    {
        return view('user::admin.profile')->withProfile($user);
    }
}
