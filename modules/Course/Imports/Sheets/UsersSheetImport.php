<?php

namespace Course\Imports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use User\Models\User;

class UsersSheetImport implements ToCollection, WithHeadingRow
{
    /**
     * @param  \Illuminate\Support\Collection $users
     * @return void
     */
    public function collection(Collection $users)
    {
        $users->each(function ($user) {
            User::updateOrCreate([
                'id' => $user['id'],
                'email' => $user['email'],
            ], $user->except('id')->toArray());
        });
    }
}
