<?php

namespace Course\Exports\Sheets;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;

class UsersSheet implements FromCollection, WithTitle, WithHeadings, WithStrictNullComparison
{
    /**
     * The collection of users to export.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $users;

    /**
     * Pass in the colletion of users.
     *
     * @param \Illuminate\Support\Collection $users
     */
    public function __construct(Collection $users)
    {
        $this->users = $users->makeVisible('password');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->users;
    }

    /**
     * Retrieve the file headings.
     *
     * @return array
     */
    public function headings(): array
    {
        if ($this->users->isEmpty()) {
            return [];
        }

        return array_keys($this->users->first()->attributesToArray());
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Users';
    }
}
