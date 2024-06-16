<?php

namespace Course\Exports\Sheets;

use Course\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;

class CoursesSheet implements FromCollection, WithTitle, WithHeadings, WithStrictNullComparison
{
    /**
     * The collection of courses to export.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $courses;

    /**
     * Pass in the colletion of courses.
     *
     * @param \\Illuminate\Database\Eloquent\Collection $courses
     */
    public function __construct(Collection $courses)
    {
        $this->courses = $courses;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->courses;
    }

    /**
     * Retrieve the file headings.
     *
     * @return array
     */
    public function headings(): array
    {
        if ($this->courses->isEmpty()) {
            return [];
        }

        return array_keys($this->courses->first()->attributesToArray());
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Courses';
    }
}
