<?php

namespace Course\Imports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Taxonomy\Models\Category;

class CategoriesSheetImport implements ToCollection, WithHeadingRow
{
    /**
     * @param  \Illuminate\Support\Collection $categories
     * @return void
     */
    public function collection(Collection $categories)
    {
        $categories->each(function ($category) {
            Category::updateOrCreate([
                'id' => $category['id'],
                'code' => $category['code'],
            ], $category->except('id')->toArray());
        });
    }
}
