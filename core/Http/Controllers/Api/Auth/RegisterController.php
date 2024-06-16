<?php

namespace Core\Http\Controllers\Api\Auth;

use Core\Http\Controllers\Api\ApiController;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use User\Models\User;

class RegisterController extends ApiController
{
    /**
     *--------------------------------------------------------------------------
     * Register Controller
     *--------------------------------------------------------------------------
     *
     * This controller handles the registration of new users as well as their
     * validation and creation. By default this controller uses a trait to
     * provide this functionality without requiring any additional code.
     *
     */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \User\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'username' => $data['email'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed                    $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        return response()->json($request->user()->createToken($user->email)->accessToken);
    }
}
