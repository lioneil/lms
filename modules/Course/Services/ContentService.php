<?php

namespace Course\Services;

use Closure;
use Core\Application\Service\Concerns\CanExtractFile;
use Core\Application\Service\Concerns\CanUploadFile;
use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Core\Rules\MimeIf;
use Course\Enumerations\LessonMetadataKeys;
use Course\Models\Content;
use Course\Models\Course;
use Course\Models\Lesson;
use Course\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use User\Models\User;

class ContentService extends Service implements ContentServiceInterface
{
    use CanUploadFile,
        CanExtractFile,
        HaveAuthorization;

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
    protected $ownable = false;

    /**
     * Constructor to bind model to a repository.
     *
     * @param \Course\Models\Content   $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Content $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }

    /**
     * Define the validation rules for the model.
     *
     * @param  integer|null $id
     * @param  string|null  $mime
     * @return array
     */
    public function rules($id = null, $mime = null): array
    {
        return [
            'title' => 'required|max:255',
            'subtitle' => 'required|max:255',
            'course_id' => 'required|numeric',
            'content' => ['required', new MimeIf($mime)],
            'type' => 'required',
            'slug' => [
                'sometimes',
                'required',
                Rule::unique($this->getTable())->ignore($id)->where(function ($query) {
                    return $query->where('course_id', $this->request->input('course_id'));
                }),
            ],
        ];
    }

    /**
     * Define the validation messages for the model.
     *
     * @return array
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Render the content to view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return $this->model->content;
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
     * Update model resource.
     *
     * @param  integer $id
     * @param  array   $attributes
     * @return boolean
     */
    public function update(int $id, array $attributes): bool
    {
        $model = $this->model()->findOrFail($id);
        $model->deleteStoredContentIfFile($attributes['content'] ?? false);
        $model = $this->save($model, array_merge($model->toArray(), $attributes));

        return $model->exists();
    }

    /**
     * Update or Save new resource to storage.
     *
     * @param  \Course\Models\Content $model
     * @param  array                  $attributes
     * @return \Course\Models\Content
     */
    public function save(Content $model, array $attributes)
    {
        $model->title = $attributes['title'];
        $model->subtitle = $attributes['subtitle'] ?? $attributes['title'];
        $model->slug = $this->handleSlug(
            $attributes['slug'] ?? $model->slug ?? $attributes['type'],
            $model->course_id ?? $attributes['course_id'] ?? null
        );
        $model->description = $attributes['description'] ?? null;
        $model->content = $this->handleContentUpload($attributes);
        $model->sort = $attributes['sort'] ?? null;
        $model->type = $attributes['type'] ?? null;
        $model->metadata = $this->handleMetadata(array_merge(
            is_null($model->metadata) ? [] : $model->metadata->toArray(),
            $attributes['metadata'] ?? []
        ));
        $model->course()->associate(Course::find($attributes['course_id']));
        $model->user()->associate(User::find($attributes['user_id'] ?? false) ?? Auth::user());
        $model->save();

        return $model;
    }

    /**
     * Validate and sanitize metadata.
     *
     * @param  array $attributes
     * @return array
     */
    public function handleMetadata($attributes)
    {
        return array_merge($attributes ?? [], ['pathname' => $this->getPathname()]);
    }

    /**
     * Validate and sanitize slug.
     *
     * @param  string  $slug
     * @param  integer $courseId
     * @param  integer $i
     * @return string
     */
    public function handleSlug($slug, $courseId, $i = 0)
    {
        $text = $slug;

        if ($this->whereSlug($text)->whereIn('course_id', [$courseId])->exists()) {
            do {
                $text = sprintf('%s-%s', Str::slug($slug), ++$i);
            } while ($this->whereSlug($text)->exists());
        }

        return $text;
    }

    /**
     * Retrieve or upload image.
     *
     * @param  array $attributes
     * @return string
     */
    public function handleContentUpload($attributes)
    {
        return is_file($attributes['content'] ?? false)
            ? $this->upload($attributes['content'])
            : $attributes['content_old'] ?? $attributes['content'] ?? null;
    }

    /**
     * Clone model resource
     *
     * @param  integer $id
     * @param  array   $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function clone($id, $attributes = [])
    {
        $content = $this->findOrFail($id);
        $model = $content->replicate();
        $model->title = sprintf('Clone of %s', $model->title);
        $model->slug = $this->handleSlug(
            $model->slug, $model->course_id ?? $attributes['course_id'] ?? null
        );
        $model->metadata = $this->handleMetadata(array_merge(
            is_null($model->metadata) ? [] : $model->metadata->toArray(),
            $attributes['metadata'] ?? []
        ));
        $model->save();

        $model->course->lessons->each(function ($content, $i) {
            $content->sort = $sort = $i + 1;
            $content->save();
        });

        return $model;
    }

    /**
     * Reorder the sort column of the contents.
     *
     * @param  array $attributes
     * @return boolean
     */
    public function reorder($attributes)
    {
        collect($attributes['contents'])->each(function ($item) {
            $content = $this->find($item['id']);
            $content->sort = $item['sort'];
            $content->metadata = $this->handleMetadata(array_merge(
                is_null($content->metadata) ? [] : $content->metadata->toArray(),
                $item['metadata'] ?? []
            ));
            $content->reorder();
        });

        return true;
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
     * Attach closures to the content.
     *
     * @param  \Course\Models\Content $content
     * @return void
     */
    public function closureAttachToSelf(Content $content)
    {
        if ($content->metadata->get('parent')) {
            $this->closureUpdateParent($content);
        } else {
            $content->attachToSelf();
        }
    }

    /**
     * Attach closures to the content.
     *
     * @param  \Course\Models\Content $content
     * @return void
     */
    public function closureUpdateParent(Content $content)
    {
        $parent = $this->model()->find($content->metadata->get('parent') ?? false);

        if ($parent) {
            $content->updateParent($parent);
        } else {
            $content->closurables()->detach();
            $content->attachToSelf();
        }
    }

    /**
     * Extract the archive filepath from metadata path.
     *
     * @param  \Course\Models\Lesson $content
     * @param  \Closure              $callback
     * @return mixed
     */
    public function extractContentOr(Lesson $content, Closure $callback = null)
    {
        try {
            if (! is_null($content->metadata) && file_exists($path = $content->metadata->get('pathname'))) {
                $destination = settings(
                    'storage:modules', 'modules/'.$this->getTable()
                ).DIRECTORY_SEPARATOR.date('Y-m-d').DIRECTORY_SEPARATOR.$content->slug;

                if ($this->extract($path, storage_path($destination))) {
                    $content->metadata = array_merge($content->metadata->toArray(), [
                        LessonMetadataKeys::ARCHIVEPATH => $destination
                    ]);

                    $content->save();

                    return $content;
                }
            }
        } catch (\Exception $e) {
            return call_user_func_array($callback, [$e]);
        }

        return $content ?? true;
    }

    /**
     * Mark the content as complete for the given student.
     *
     * @param  \Course\Models\Content $content
     * @param  array                  $attributes
     * @return \Course\Models\Content
     */
    public function complete(Content $content, array $attributes)
    {
        $content->markAsComplete(Student::find($attributes['user_id']));

        return $content;
    }
}
