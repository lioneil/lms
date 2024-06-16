<?php

namespace Course\Imports\Sheets;

use Course\Models\Lesson;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LessonsSheetImport implements ToCollection, WithHeadingRow
{
    /**
     * @param  \Illuminate\Support\Collection $lessons
     * @return void
     */
    public function collection(Collection $lessons)
    {
        $lessons->each(function ($lesson) {
            Lesson::updateOrCreate([
                'id' => $lesson['id'],
                'slug' => $lesson['slug'],
                'course_id' => $lesson['course_id'],
            ], $lesson->except('id')->toArray());
        });
    }
}
