<?php

namespace Assessment\Http\Requests;

use Assessment\Services\FieldServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

class OwnedFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            FieldServiceInterface::class
        )->authorize($this->field, 'fields');
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
