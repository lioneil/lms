<?php

namespace Course\Exports\Sheets;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;

class TagsSheet implements FromCollection, WithTitle, WithHeadings, WithStrictNullComparison
{
    /**
     * The collection of tags to export.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $tags;

    /**
     * Pass in the colletion of tags.
     *
     * @param \Illuminate\Support\Collection $tags
     */
    public function __construct(Collection $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->tags;
    }

    /**
     * Retrieve the file headings.
     *
     * @return array
     */
    public function headings(): array
    {
        return ['id', 'name', 'icon', 'type'];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Tags';
    }
}
