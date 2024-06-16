<?php

namespace Assessment\Imports\Sheets;

use Assessment\Models\Assessment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssessmentsSheetImport implements ToCollection, WithHeadingRow
{
    /**
     * @param  \Illuminate\Support\Collection $assessments
     * @return void
     */
    public function collection(Collection $assessments)
    {
        $assessments->each(function ($assessment) {
            Assessment::updateOrCreate([
                'id' => $assessment['id'],
                'title' => $assessment['title'],
                'subtitle' => $assessment['subtitle'],
                'description' => $assessment['description'],
                'slug' => $assessment['slug'],
                'url' => $assessment['url'],
                'method' => $assessment['method'],
                'type' => $assessment['type'],
                'metadata' => $assessment['metadata'],
                'template_id' => $assessment['template_id'],
                'user_id' => $assessment['user_id'],
            ], $assessment->except('id')->toArray());
        });
    }
}
