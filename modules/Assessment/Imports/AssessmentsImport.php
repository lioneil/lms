<?php

namespace Assessment\Imports;

use Assessment\Models\Assessment;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AssessmentsImport implements WithMultipleSheets, WithHeadingRow
{
    use Importable;

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            'Assessments' => new Sheets\AssessmentsSheetImport,
        ];
    }
}
