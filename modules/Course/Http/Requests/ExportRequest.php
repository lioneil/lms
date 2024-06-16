<?php

namespace Course\Http\Requests;

use Course\Services\CourseServiceInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Excel;

class ExportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            CourseServiceInterface::class
        )->authorize($this->course);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'filename' => 'required',
            'format' => [
                'required', Rule::in([
                    Excel::CSV, Excel::XLSX, Excel::ODS,
                    Excel::XLS, Excel::HTML, Excel::DOMPDF,
                ]),
            ],
        ];
    }
}
