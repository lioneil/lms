<?php

namespace Course\Http\Request;

use Illuminate\Foundation\Http\FormRequest;
use Course\Services\CategoryServiceInterface;

class OwnedCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            CategoryServiceInterface::class
        )->authorize($this->category, 'categories');
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
