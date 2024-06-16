<?php

namespace Assessment\Http\Requests;

use Assessment\Services\AssessmentServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

class AssessmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            AssessmentServiceInterface::class
        )->authorize($this->assessment, 'forms');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->container->make(
            AssessmentServiceInterface::class
        )->rules($this->assessment);
    }
}
