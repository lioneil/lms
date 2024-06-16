<?php

namespace Library\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Library\Services\LibraryServiceInterface;

class OwnedLibraryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            LibraryServiceInterface::class
        )->authorize($this->library, 'libraries');
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
