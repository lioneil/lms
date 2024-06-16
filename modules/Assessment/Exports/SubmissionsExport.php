<?php

namespace Assessment\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class SubmissionsExport implements FromView, Responsable
{
    use Exportable;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     *
     * @var string
     */
    private $fileName = 'submissions.csv';

    /**
     * Optional headers.
     *
     * @var array
     */
    private $headers = [
        'Content-Type' => 'text/csv',
    ];

    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view('submission::exports.submissions', [
            'submissions' => Submission::all(),
        ]);
    }
}
