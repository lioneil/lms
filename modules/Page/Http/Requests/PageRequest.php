<?php

namespace Page\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Page\Services\PageServiceInterface;

class PageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            PageServiceInterface::class
        )->authorize($this->page, 'pages');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->container->make(
            PageServiceInterface::class
        )->rules($this->page);
    }
}
