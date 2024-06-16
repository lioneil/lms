<?php

namespace Page\Services;

use Core\Application\Service\Concerns\CanUploadFile;
use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Concerns\HavePublishables;
use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Page\Models\Page;
use Taxonomy\Models\Category;
use Template\Models\Template;
use User\Models\User;

class PageService extends Service implements PageServiceInterface
{
    use CanUploadFile,
        HaveAuthorization,
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
     * Property to check if model is ownable
     *
     * @var boolean
     */
    protected $ownable = true;

    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'pages';

    /**
     * Constructor to bind model to a repository.
     *
     * @param \Page\Models\Page        $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Page $model, Request $request)
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
            'feature' => 'required|max:255',
            'template_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'code' => ['required', 'alpha_dash', Rule::unique($this->getTable())->ignore($id)],
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
     * Update model resource.
     *
     * @param  integer $id
     * @param  array   $attributes
     * @return boolean
     */
    public function update(int $id, array $attributes): bool
    {
        $model = $this->model()->findOrFail($id);
        $model = $this->save($model, $attributes);

        return $model->exists();
    }

    /**
     * Update or Save new resource to storage.
     *
     * @param  \Page\Models\Page $model
     * @param  array             $attributes
     * @return \Page\Models\Page
     */
    public function save(Page $model, array $attributes)
    {
        $model->title = $attributes['title'];
        $model->code = $attributes['code'];
        $model->feature = $this->handleFeatureUpload($attributes);
        $model->body = $attributes['body'] ?? null;
        $model->template()->associate(Template::find($attributes['template_id']));
        $model->user()->associate(User::find($attributes['user_id']));
        $model->category()->associate($this->handleCategory($attributes));
        $model->publishBy($attributes['published_at'] ?? null);
        $model->save();

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
     * Retrieve or upload feature.
     *
     * @param  array $attributes
     * @return string
     */
    protected function handleFeatureUpload($attributes)
    {
        return is_file($attributes['feature'] ?? false)
            ? $this->upload($attributes['feature'])
            : $attributes['feature_old'] ?? $attributes['feature'] ?? null;
    }
}
