<?php

namespace Assessment\Services\Concerns;

use Assessment\Exports\SubmissionsExport;
use Maatwebsite\Excel\Excel;

trait ExportableSubmission
{
    /**
     * Export a resource or resources to a human-readable
     * format. E.g. PDF, Spreadsheet, CSV, etc.
     *
     * @param  string $format
     * @return mixed
     */
    public function export(string $format)
    {
        return new SubmissionsExport($format);
    }
}
