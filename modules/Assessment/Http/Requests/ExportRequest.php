<?php

namespace Assessment\Http\Requests;

use Assessment\Services\SubmissionServiceInterface;
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
            SubmissionServiceInterface::class
        )->authorize($this->submission);
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
