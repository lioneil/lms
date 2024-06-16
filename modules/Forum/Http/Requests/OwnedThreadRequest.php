<?php

namespace Forum\Http\Requests;

use Forum\Services\ThreadServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

class OwnedThreadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            ThreadServiceInterface::class
        )->authorize($this->thread, 'threads');
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
