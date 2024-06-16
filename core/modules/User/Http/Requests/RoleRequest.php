<?php

namespace User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use User\Services\RoleServiceInterface;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            RoleServiceInterface::class
        )->authorize();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->container->make(
            RoleServiceInterface::class
        )->rules($this->route('role'));
    }
}
