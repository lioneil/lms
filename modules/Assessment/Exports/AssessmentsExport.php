<?php

namespace Assessment\Exports;

use Assessment\Models\Assessment;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Excel;
use User\Models\User;

class AssessmentsExport implements WithMultipleSheets, Responsable, WithStrictNullComparison
{
    use Exportable;

    /**
     * The collection of assessments to export.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $assessments;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     *
     * @var string $filename
     */
    private $fileName = 'assessments.xlsx';

    /**
     * Optional Writer Type
     *
     * @var string $writerType
     */
    private $writerType = Excel::XLSX;

    /**
     * Modify default filename and writer type.
     *
     * @param \\Illuminate\Database\Eloquent\Collection $assessments
     * @param string                                    $fileName
     * @param string                                    $writerType
     */
    public function __construct(Collection $assessments, $fileName = null, $writerType = Excel::XLSX)
    {
        $this->assessments = $assessments;
        $this->fileName = $fileName ?? $this->fileName;
        $this->writerType = $writerType ?? $this->writerType;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            new Sheets\AssessmentsSheet($this->getAssessments()),
        ];
    }

    /**
     * Retrieve the properties used in the exported file.
     *
     * @return array
     */
    public function properties(): array
    {
        return [
            'creator'        => settings('app:title'),
            'lastModifiedBy' => settings('app:title'),
            'title'          => $this->fileName,
            'company'        => settings('app:title'),
        ];
    }

    /**
     * Retrieve the courses collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getAssessments()
    {
        return $this->assessments;
    }
}
