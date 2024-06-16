<?php

namespace User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use User\Services\UserServiceInterface;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized
     * to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->container->make(
            UserServiceInterface::class
        )->rules($this->route('user'));
    }
}
