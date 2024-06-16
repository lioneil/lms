<?php

namespace Course\Imports\Sheets;

use Course\Models\Lessonstree;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LessonstreeSheetImport implements ToCollection, WithHeadingRow
{
    /**
     * @param  \Illuminate\Support\Collection $lessonstrees
     * @return void
     */
    public function collection(Collection $lessonstrees)
    {
        $lessonstrees->each(function ($lessonstree) {
            Lessonstree::updateOrCreate([
                'ancestor_id' => $lessonstree['ancestor_id'],
                'descendant_id' => $lessonstree['descendant_id'],
                'depth' => $lessonstree['depth'],
                'root' => $lessonstree['root'],
            ], $lessonstree->toArray());
        });
    }
}
