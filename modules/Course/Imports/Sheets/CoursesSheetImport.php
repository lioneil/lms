<?php

namespace Course\Imports\Sheets;

use Course\Models\Course;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CoursesSheetImport implements ToCollection, WithHeadingRow
{
    /**
     * @param  \Illuminate\Support\Collection $courses
     * @return void
     */
    public function collection(Collection $courses)
    {
        $courses->each(function ($course) {
            Course::updateOrCreate([
                'id' => $course['id'],
                'code' => $course['code'],
            ], $course->except('id')->toArray());
        });
    }
}
