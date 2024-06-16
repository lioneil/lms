<?php

namespace Material\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Material\Services\MaterialServiceInterface;

class OwnedMaterialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            MaterialServiceInterface::class
        )->authorize($this->material, 'coursewares');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
