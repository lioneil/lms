<?php

namespace Announcement\Services;

use Announcement\Models\Announcement;
use Core\Application\Service\Concerns\CanUploadFile;
use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Concerns\HavePublishables;
use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Taxonomy\Models\Category;
use User\Models\User;

class AnnouncementService extends Service implements AnnouncementServiceInterface
{
    use HaveAuthorization,
        CanUploadFile,
        HavePublishables;

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
     * The Request Instance.
     *
     * @var boolean
     */
    protected $ownable = true;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'announcements';

    /**
     * Constructor to bind model to a repository.
     *
     * @param \Announcement\Models\Announcement $model
     * @param \Illuminate\Http\Request          $request
     */
    public function __construct(Announcement $model, Request $request)
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
            'body' => 'required',
            'user_id' => 'required|numeric',
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
     * Retrieve or upload photo.
     *
     * @param  array $attributes
     * @return string
     */
    protected function handlePhotoUpload($attributes)
    {
        return is_file($attributes['photo'] ?? false)
            ? $this->upload($attributes['photo'])
            : $attributes['photo_old'] ?? $attributes['photo'] ?? null;
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
        $model = $this->model->findOrFail($id);
        $model = $this->save($model, array_merge($model->toArray(), $attributes));

        return $model->exists();
    }

    /**
     * Create or Update the passed attributes.
     *
     * @param  \Announcement\Models\Announcement $model
     * @param  array                             $attributes
     * @return \Announcement\Models\Announcement
     */
    protected function save(Announcement $model, array $attributes)
    {
        $model->title = $attributes['title'];
        $model->slug = Str::slug($attributes['slug'] ?? $attributes['title']);
        $model->body = $attributes['body'];
        $model->photo = $this->handlePhotoUpload($attributes);
        $model->pathname = $this->getPathname() ?? $attributes['pathname'] ?? $model->pathname ?? null;
        $model->type = $attributes['type'] ?? $this->getTypeSignature();
        $model->user()->associate(User::find($attributes['user_id']));
        $model->category()->associate($this->handleCategory($attributes));
        $model->published_at = $attributes['published_at'];
        $model->expired_at = $attributes['expired_at'];
        $model->save();

        return $model;
    }

    /**
     * Remove the expired announcements.
     *
     * @param  Announcement\Models\Announcement $softDelete
     * @return void
     */
    public function autoclean($softDelete = null)
    {
        if (is_null($softDelete)) {
            $this->model()->where('expired_at', '<', now())->delete();
        } else {
            $this->model()->onlyTrashed()->where('deleted_at', '<', now()->subDays(5)->getTimeStamp())
            ->forceDelete();
        }
    }
}
