<?php

namespace Taxonomy\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Taxonomy\Services\TagServiceInterface;

class TagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            TagServiceInterface::class
        )->authorize($this->tag, 'tags');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->container->make(
            TagServiceInterface::class
        )->rules();
    }
}
