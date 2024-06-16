<?php

namespace Widget\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Widget\Services\WidgetServiceInterface;

class DashboardWidgetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            WidgetServiceInterface::class
        )->authorize($this->widget, 'widgets');
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
