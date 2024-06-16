<?php

namespace Assessment\Exports\Sheets;

use Assessment\Models\Assessment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;

class AssessmentsSheet implements FromCollection, WithTitle, WithHeadings, WithStrictNullComparison
{
    /**
     * The collection of assessments to export.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $assessments;

    /**
     * Pass in the colletion of assessments.
     *
     * @param \\Illuminate\Database\Eloquent\Collection $assessments
     */
    public function __construct(Collection $assessments)
    {
        $this->assessments = $assessments;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->assessments;
    }

    /**
     * Retrieve the file headings.
     *
     * @return array
     */
    public function headings(): array
    {
        if ($this->assessments->isEmpty()) {
            return [];
        }

        return array_keys($this->assessments->first()->attributesToArray());
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Assessments';
    }
}
