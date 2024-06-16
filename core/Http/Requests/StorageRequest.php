<?php

namespace Core\Http\Requests;

use Core\Services\FileServiceInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized
     * to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return Auth::check() && Auth::user()->can('files.upload');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'required|file|mimes:png,jpeg,gif,webp',
        ];
    }
}
