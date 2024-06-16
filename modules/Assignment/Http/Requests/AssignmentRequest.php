<?php

namespace Assignment\Http\Requests;

use Assignment\Services\AssignmentServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

class AssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            AssignmentServiceInterface::class
        )->authorize($this->assignment, 'assignments');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->container->make(
            AssignmentServiceInterface::class
        )->rules();
    }
}
