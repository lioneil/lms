<?php

namespace Setting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Setting\Services\SettingServiceInterface;

class OwnedSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            SettingServiceInterface::class
        )->authorize($this->setting, 'settings');
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
