<?php

namespace Course\Services;

use Core\Application\Service\Concerns\CanUploadFile;
use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Concerns\HavePublishables;
use Core\Application\Service\Service;
use Course\Exports\CoursesExport;
use Course\Imports\CoursesImport;
use Course\Models\Content;
use Course\Models\Course;
use Course\Services\Concerns\HaveCoursewares;
use Favorite\Services\HaveFavoritables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Subscription\Services\HaveProgressibles;
use Subscription\Services\HaveSubscribables;
use Taxonomy\Models\Category;
use Taxonomy\Services\HaveTaggables;
use User\Models\User;

class CourseService extends Service implements CourseServiceInterface
{
    use CanUploadFile,
        HaveCoursewares,
        HaveAuthorization,
        HaveFavoritables,
        HaveProgressibles,
        HavePublishables,
        HaveSubscribables,
        HaveTaggables;

    /**
     * The property on class instances.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Property to check if model is ownable.
     *
     * @var boolean
     */
    protected $ownable = true;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'courses';

    /**
     * Constructor to bind model to a repository.
     *
     * @param \Course\Models\Course    $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Course $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }

    /**
     * Define the validation rules for the model.
     *
     * @param  integer|null $id
     * @return array
     */
    public function rules($id = null): array
    {
        return [
            'title' => 'required|max:255',
            'subtitle' => 'required|max:255',
            'user_id' => 'required|numeric',
            'image' => 'required_without:image_old',
            'slug' => ['required', 'alpha_dash', Rule::unique($this->getTable())->ignore($id)],
            'code' => ['required', 'regex:/[a-zA-Z0-9\s]+/', Rule::unique($this->getTable())->ignore($id)],
        ];
    }

    /**
     * Define the validation messages for the model.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'image.required_without' => 'The image field is required'
        ];
    }

    /**
     * Retrieve the storage path for courses.
     *
     * @return string
     */
    protected function getStoragePath(): string
    {
        return storage_path(
            'modules'.DIRECTORY_SEPARATOR.$this->getTable().
            DIRECTORY_SEPARATOR.date('Y-m-d')
        );
    }

    /**
     * Create model resource.
     *
     * @param  array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $attributes)
    {
        $model = $this->model();

        return $this->save($model, $attributes);
    }

    /**
     * Handle the updated content's file from storage.
     *
     * @param  \Course\Models\Course $model
     * @param  array                 $attributes
     * @return void
     */
    protected function handleImageFileFromStorage(Course $model, $attributes)
    {
        if (is_file($attributes['image'] ?? false)) {
            $this->deleteFileFromStorage(is_null($model->metadata) ? false : $model->metadata->get('pathname'));
        }
    }

    /**
     * Update model resource.
     *
     * @param  integer $id
     * @param  array   $attributes
     * @return boolean
     */
    public function update(int $id, array $attributes): bool
    {
        $model = $this->model()->findOrFail($id);
        $this->handleImageFileFromStorage($model, $attributes);
        $model = $this->save($model, array_merge($model->toArray(), $attributes));

        return $model->exists();
    }

    /**
     * Update or save new resource to storage.
     *
     * @param  \Course\Models\Course $model
     * @param  array                 $attributes
     * @return \Course\Models\Course
     */
    public function save(Course $model, array $attributes)
    {
        $model->title = $attributes['title'];
        $model->subtitle = $attributes['subtitle'];
        $model->slug = Str::slug($attributes['slug']);
        $model->code = $attributes['code'];
        $model->description = $attributes['description'] ?? null;
        $model->icon = $attributes['icon'] ?? null;
        $model->image = $this->handleImageUpload($attributes);
        $model->metadata = array_merge($attributes['metadata'] ?? [], [
            'pathname' => $this->getPathname()
                ?? is_null($model->metadata) ? null : $model->metadata->get('pathname')
        ]);
        $model->user()->associate(User::find($attributes['user_id']));
        $model->category()->associate($this->handleCategory($attributes));
        $model->publishBy($attributes['published_at'] ?? null);
        $model->save();

        $model->tags()->sync($this->getOrSaveTags(
            $attributes['tags'] ?? []
        )->pluck('id')->toArray());

        $this->handleCoursewares($model, $attributes['coursewares'] ?? []);

        return $model;
    }

    /**
     * Permanently delete model resource.
     *
     * @param  integer|array $id
     * @return void
     */
    public function delete($id)
    {
        $this
            ->model()
            ->withTrashed()
            ->whereIn($this->model()->getKeyName(), (array) $id)
            ->get()->each(function ($model) {
                $this->deleteFileFromStorage(
                    is_null($model->metadata) ? false : $model->metadata->get('pathname')
                );
                $model->forceDelete();
            });
    }

    /**
     * Soft delete model resource.
     *
     * @param  integer|array $id
     * @return void
     */
    public function destroy($id)
    {
        $this
            ->model()
            ->whereIn($this->model()->getKeyName(), (array) $id)
            ->get()->each(function ($model) {
                $model->delete();
            });
    }

    /**
     * Save the category to storage.
     *
     * @param  array $attributes
     * @return \Taxonomy\Models\Category
     */
    protected function handleCategory($attributes)
    {
        $category = Category::find($attributes['category_id'] ?? null);

        if (is_null($category) && isset($attributes['category_id'])) {
            $category = Category::firstOrNew([
                'code' => Str::slug($attributes['category_id'])
            ]);
            $category->name = $name = $attributes['category_id'];
            $category->alias = $name;
            $category->code = Str::slug($name);
            $category->type = Str::singular($this->getTable());
            $category->user()->associate(Auth::user());
            $category->save();
        }

        return $category;
    }

    /**
     * Retrieve or upload image.
     *
     * @param  array $attributes
     * @return string
     */
    protected function handleImageUpload($attributes)
    {
        return is_file($attributes['image'] ?? false)
            ? $this->upload($attributes['image'])
            : $attributes['image_old'] ?? $attributes['image'] ?? null;
    }

    /**
     * Export a resource or resources to a human-readable
     * format. E.g. PDF, Spreadsheet, CSV, etc.
     *
     * @param  array  $attributes
     * @param  string $format
     * @param  string $filename
     * @return mixed
     */
    public function export(array $attributes, string $format, string $filename = null)
    {
        $courses = $this->whereIn('id', $attributes['id'])->get();

        return new CoursesExport($courses, $filename, $format);
    }

    /**
     * Import from array, file, or any resource.
     *
     * @param  array|mixed $file
     * @return void
     */
    public function import($file)
    {
        with(new CoursesImport)->import($file);
    }

    /**
     * Update or create the model's progress.
     *
     * @param  \Course\Models\Course $course
     * @param  array                 $attributes
     * @return \Subscription\Models\Progression
     */
    public function progress(Course $course, array $attributes)
    {
        $progress = $course->progressions()->firstOrNew(['user_id' => $attributes['user_id']]);
        $lessons = $progress->metadata['lessons'];
        $lessons = collect($lessons)->map(function ($lesson) use ($attributes) {
            $updatedLesson = collect($attributes['metadata']['lessons'])->where('id', $lesson['id'])->first();
            return array_merge($lesson, $updatedLesson ?? []);
        });
        $progress->status = $attributes['status'] ?? $progress->status;
        $progress->results = $attributes['results'] ?? $progress->results;
        $progress->metadata = array_merge($attributes['metadata'], ['lessons' => $lessons->toArray()]);
        $progress->save();

        return $progress;
    }
}
