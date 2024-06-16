<?php

namespace Assessment\Http\Requests;

use Assessment\Services\SubmissionServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

class OwnedSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            SubmissionServiceInterface::class
        )->authorize($this->submission, 'submissions');
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
