<?php

namespace Course\Exports;

use Course\Models\Course;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Excel;
use User\Models\User;

class CoursesExport implements WithMultipleSheets, Responsable, WithStrictNullComparison
{
    use Exportable;

    /**
     * The collection of courses to export.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $courses;

    /**
     * It's required to define the fileName within
     * the export class when making use of Responsable.
     *
     * @var string $filename
     */
    private $fileName = 'courses.xlsx';

    /**
     * Optional Writer Type
     *
     * @var string $writerType
     */
    private $writerType = Excel::XLSX;

    /**
     * Modify default filename and writer type.
     *
     * @param \\Illuminate\Database\Eloquent\Collection $courses
     * @param string                                    $fileName
     * @param string                                    $writerType
     */
    public function __construct(Collection $courses, $fileName = null, $writerType = Excel::XLSX)
    {
        $this->courses = $courses;
        $this->fileName = $fileName ?? $this->fileName;
        $this->writerType = $writerType ?? $this->writerType;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            new Sheets\CoursesSheet($this->getCourses()),
            new Sheets\LessonsSheet($this->getLessons()),
            new Sheets\LessonstreeSheet($this->getLessonstree()),
            new Sheets\CategoriesSheet($this->getCategories()),
            new Sheets\TagsSheet($this->getTags()),
            new Sheets\TaggablesSheet($this->getTaggables()),
            new Sheets\UsersSheet($this->getUsers()),
        ];
    }

    /**
     * Retrieve the properties used in the exported file.
     *
     * @return array
     */
    public function properties(): array
    {
        return [
            'creator'        => settings('app:title'),
            'lastModifiedBy' => settings('app:title'),
            'title'          => $this->fileName,
            'company'        => settings('app:title'),
        ];
    }

    /**
     * Retrieve the courses collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getCourses()
    {
        return $this->courses;
    }

    /**
     * Retrieve the lessons collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getLessons()
    {
        return $this->courses->flatMap->lessons;
    }

    /**
     * Retrieve the lessons collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getLessonstree()
    {
        $lessons = $this->courses->flatMap->lessons->pluck('id')->toArray();

        return DB::table('lessonstree')->whereIn('descendant_id', $lessons)->get();
    }

    /**
     * Retrieve the categories collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getCategories()
    {
        return $this->courses->map->category->reject(function ($item) {
            return is_null($item);
        });
    }

    /**
     * Retrieve the tags collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getTags()
    {
        return $this->courses->flatMap->tags;
    }

    /**
     * Retrieve the tags collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getTaggables()
    {
        return $this->courses->flatMap->tags->map->pivot;
    }

    /**
     * Retrieve the users collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getUsers()
    {
        $users = $this->courses->pluck('user_id');
        $users = $users->mapWithKeys(function ($item) {
            return ["o$item" => $item];
        })->toArray();
        $lessons = $this->courses->flatMap->lessons->pluck('user_id');
        $lessons = $lessons->mapWithKeys(function ($item) {
            return ["o$item" => $item];
        })->toArray();
        $categories = $this->getCategories()->pluck('user_id');
        $categories = $categories->mapWithKeys(function ($item) {
            return ["o$item" => $item];
        })->toArray();

        return User::whereIn('id', array_merge($users, $lessons, $categories))->get();
    }
}
