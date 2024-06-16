<?php

namespace Forum\Services;

use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Forum\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Taxonomy\Models\Category;
use User\Models\User;

class ThreadService extends Service implements ThreadServiceInterface
{
    use HaveAuthorization;

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
     * Constructor to bind model to a repository.
     *
     * @param \Forum\Models\Thread     $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Thread $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }

    /**
     * Define the validation rules for the model
     *
     * @param  integer|null $id
     * @return array
     */
    public function rules($id = null): array
    {
        return [
            'title' => 'required|max:255',
            'user_id' => 'required|numeric',
            'slug' => ['required', 'alpha_dash', Rule::unique($this->getTable())->ignore($id)],
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
     * Create model resource.
     *
     * @param  array $attributes
     * @return \Illuminate\Http\Response
     */
    public function store(array $attributes)
    {
        $model = $this->model;

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
        $model = $this->save($model, $attributes);

        return $model->exists();
    }

    /**
     * Update or Save new resource to storage.
     *
     * @param  \Forum\Models\Thread $model
     * @param  array                $attributes
     * @return \Forum\Models\Thread
     */
    public function save(Thread $model, $attributes)
    {
        $model->title = $attributes['title'];
        $model->slug = Str::slug($attributes['slug']);
        $model->body = $attributes['body'] ?? null;
        $model->type = $attributes['type'];
        $model->user()->associate(User::find($attributes['user_id']));
        $model->category()->associate($this->handleCategory($attributes));
        $model->locked_at = $attributes['locked_at'] ?? null;
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
    public function handleCategory($attributes)
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
     * Like the given resource.
     *
     * @param  \Forum\Models\Thread $thread
     * @param  array                $attributes
     * @return \Forum\Models\Thread
     */
    public function like(Thread $thread, $attributes)
    {
        return $thread->like(User::find($attributes['user_id']));
    }

    /**
     * Dislike the given resource.
     *
     * @param  \Forum\Models\Thread $thread
     * @param  array                $attributes
     * @return \Forum\Models\Thread
     */
    public function dislike(Thread $thread, $attributes)
    {
        return $thread->dislike(User::find($attributes['user_id']));
    }
}
