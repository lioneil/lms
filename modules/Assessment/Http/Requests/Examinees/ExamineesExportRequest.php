<?php

namespace Assessment\Http\Requests\Examinees;

use Assessment\Services\FieldServiceInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Excel;

class ExamineesExportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return $this->container->make(
            FieldServiceInterface::class
        )->authorize($this->field);
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
                'required',
                Rule::in([
                    Excel::CSV, Excel::XLSX, Excel::TSV,
                    Excel::ODS, Excel::XLS, Excel::HTML, Excel::DOMPDF,
                ]),
            ],
        ];
    }
}
