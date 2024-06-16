<?php

namespace Course\Exports\Sheets;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;

class LessonsSheet implements FromCollection, WithTitle, WithHeadings, WithStrictNullComparison
{
    /**
     * The collection of lessons to export.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $lessons;

    /**
     * Pass in the colletion of lessons.
     *
     * @param \Illuminate\Support\Collection $lessons
     */
    public function __construct(Collection $lessons)
    {
        $this->lessons = $lessons;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->lessons;
    }

    /**
     * Retrieve the file headings.
     *
     * @return array
     */
    public function headings(): array
    {
        if ($this->lessons->isEmpty()) {
            return [];
        }

        return array_keys($this->lessons->first()->attributesToArray());
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Lessons';
    }
}
