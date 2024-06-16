<?php

namespace Forum\Http\Requests;

use Core\Application\Permissions\RemoveApiPrefixFromPermission;
use Forum\Services\ThreadServiceInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReactionRequest extends FormRequest
{
    use RemoveApiPrefixFromPermission;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return Auth::user()->can($this->removeApiPrefixFromPermission($this->route()->getName()));
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
