<?php

namespace Course\Exports\Sheets;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;

class LessonstreeSheet implements FromCollection, WithTitle, WithHeadings, WithStrictNullComparison
{
    /**
     * The collection of lessonstree to export.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $lessonstree;

    /**
     * Pass in the colletion of lessonstree.
     *
     * @param \Illuminate\Support\Collection $lessonstree
     */
    public function __construct(Collection $lessonstree)
    {
        $this->lessonstree = $lessonstree;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->lessonstree->map(function ($tree) {
            return [
                'ancestor_id' => $tree->ancestor_id,
                'descendant_id' => $tree->descendant_id,
                'depth' => $tree->depth,
                'root' => $tree->root,
            ];
        });
    }

    /**
     * Retrieve the file headings.
     *
     * @return array
     */
    public function headings(): array
    {
        if ($this->lessonstree->isEmpty()) {
            return [];
        }

        return [
            'ancestor_id', 'descendant_id',
            'depth', 'root',
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Lessonstree';
    }
}
