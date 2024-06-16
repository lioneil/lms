<?php

namespace Course\Imports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Taxonomy\Models\Taggable;

class TaggablesSheetImport implements ToCollection, WithHeadingRow
{
    /**
     * @param  \Illuminate\Support\Collection $taggables
     * @return void
     */
    public function collection(Collection $taggables)
    {
        $taggables->each(function ($taggable) {
            Taggable::updateOrCreate([
                'tag_id' => $taggable['tag_id'],
                'taggable_id' => $taggable['taggable_id'],
                'taggable_type' => $taggable['taggable_type'],
            ], $taggable->toArray());
        });
    }
}
