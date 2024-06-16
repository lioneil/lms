<?php

namespace Course\Http\Requests;

use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            CourseServiceInterface::class
        )->authorize($this->course, 'courses');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->container->make(
            CourseServiceInterface::class
        )->rules($this->course);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return $this->container->make(
            CourseServiceInterface::class
        )->messages();
    }
}
