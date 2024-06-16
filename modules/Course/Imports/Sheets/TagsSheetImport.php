<?php

namespace Course\Imports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Taxonomy\Models\Tag;

class TagsSheetImport implements ToCollection, WithHeadingRow
{
    /**
     * @param  \Illuminate\Support\Collection $tags
     * @return void
     */
    public function collection(Collection $tags)
    {
        $tags->each(function ($tag) {
            Tag::updateOrCreate([
                'id' => $tag['id'],
                'name' => $tag['name'],
                'type' => $tag['type'],
            ], $tag->except('id')->toArray());
        });
    }
}
