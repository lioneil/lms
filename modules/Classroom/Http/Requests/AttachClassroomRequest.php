<?php

namespace Classroom\Http\Requests;

use Classroom\Services\ClassroomServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

class AttachClassroomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            ClassroomServiceInterface::class
        )->authorize($this->classroom);
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
