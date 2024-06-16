<?php

namespace Course\Http\Requests;

use Course\Services\ContentServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

class OwnedContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            ContentServiceInterface::class
        )->authorize($this->content, 'contents');
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
