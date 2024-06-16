<?php

namespace Comment\Http\Requests;

use Comment\Services\CommentServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

class OwnedCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            CommentServiceInterface::class
        )->authorize($this->comment, 'comments');
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
