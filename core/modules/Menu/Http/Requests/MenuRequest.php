<?php

namespace Menu\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Menu\Services\MenuServiceInterface;

class MenuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            MenuServiceInterface::class
        )->authorize($this->menu, 'menus');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->container->make(
            MenuServiceInterface::class
        )->rules($this->menu);
    }
}
