<?php

namespace Announcement\Http\Requests;

use Announcement\Services\CategoryServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        return $this->container->make(
            CategoryServiceInterface::class
        )->rules($this->category);
    }
}
