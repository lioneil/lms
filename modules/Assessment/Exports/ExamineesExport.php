<?php

namespace Assessment\Exports;

use Assessment\Models\Field;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ExamineesExport implements FromView, Responsable
{
    use Exportable;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     *
     * @var string
     */
    private $fileName = 'examinees.csv';

    /**
     * Optional headers.
     *
     * @var array
     */
    private $headers = [
        'Cotent-Type' => 'text/csv',
    ];

    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view('assessment::exports.examinees', [
            'examinees' => Field::all(),
        ]);
    }
}
