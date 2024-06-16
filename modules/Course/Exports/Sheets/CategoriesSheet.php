<?php

namespace Course\Exports\Sheets;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;

class CategoriesSheet implements FromCollection, WithTitle, WithHeadings, WithStrictNullComparison
{
    /**
     * The collection of categories to export.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $categories;

    /**
     * Pass in the colletion of categories.
     *
     * @param \Illuminate\Support\Collection $categories
     */
    public function __construct(Collection $categories)
    {
        $this->categories = $categories;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->categories;
    }

    /**
     * Retrieve the file headings.
     *
     * @return array
     */
    public function headings(): array
    {
        if ($this->categories->isEmpty()) {
            return [];
        }

        return array_keys($this->categories->first()->attributesToArray());
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Categories';
    }
}
