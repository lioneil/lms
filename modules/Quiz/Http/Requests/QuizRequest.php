<?php

namespace Quiz\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Quiz\Services\QuizServiceInterface;

class QuizRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            QuizServiceInterface::class
        )->authorize($this->quiz, 'forms');
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->container->make(
            QuizServiceInterface::class
        )->rules($this->quiz);
    }
}
