<?php

namespace User\Http\Controllers;

use Core\Http\Controllers\AdminController;
use Core\Http\Controllers\Controller;
use Illuminate\Http\Request;
use User\Models\User;

class ProfileController extends Controller
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
