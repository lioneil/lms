<?php

namespace Course\Imports;

use Course\Models\Course;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CoursesImport implements WithMultipleSheets, WithHeadingRow
{
    use Importable;

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            'Users' => new Sheets\UsersSheetImport,
            'Categories' => new Sheets\CategoriesSheetImport,
            'Courses' => new Sheets\CoursesSheetImport,
            'Lessons' => new Sheets\LessonsSheetImport,
            'Lessonstree' => new Sheets\LessonstreeSheetImport,
            'Tags' => new Sheets\TagsSheetImport,
            'Taggables' => new Sheets\TaggablesSheetImport,
        ];
    }
}
