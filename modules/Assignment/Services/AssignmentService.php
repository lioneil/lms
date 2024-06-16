<?php

namespace Assignment\Services;

use Assignment\Models\Assignment;
use Core\Application\Service\Concerns\CanUploadFile;
use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use User\Models\User;

class AssignmentService extends Service implements AssignmentServiceInterface
{
    use CanUploadFile,
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
    protected $ownable = true;

    /**
     * Constructor to bind model to a repository.
     *
     * @param \Assignment\Models\Assignment $model
     * @param \Illuminate\Http\Request      $request
     */
    public function __construct(Assignment $model, Request $request)
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
            'user_id' => 'required|numeric',
            'uri' => 'required',
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
        $this->handleFileFromStorage($model, $attributes);
        $model = $this->save($model, array_merge($model->toArray(), $attributes));

        return $model->exists();
    }

    /**
     * Create or Update the passed attributes.
     *
     * @param  \Assignment\Models\Assignment $model
     * @param  array                         $attributes
     * @return \Assignment\Models\Assignment
     */
    public function save(Assignment $model, array $attributes)
    {
        $model->title = $attributes['title'];
        $model->uri = $this->handleFileUpload($attributes);
        $model->pathname = $this->getPathname() ?? $attributes['pathname'] ?? $model->pathname ?? null;
        $model->type = $attributes['type'] ?? $model->type ?? null;
        $model->coursewareable()->associate($this->getModelResourceFromString(
            $attributes['coursewareable_type'], $attributes['coursewareable_id']
        ));
        $model->user()->associate(User::find($attributes['user_id']));
        $model->save();

        return $model;
    }

    /**
     * Retrieve or Upload uri.
     *
     * @param  array $attributes
     * @return string
     */
    public function handleFileUpload($attributes)
    {
        return is_file($attributes['uri'] ?? false)
            ? $this->upload($attributes['uri'])
            : $attributes['uri_old'] ?? $attributes['uri'] ?? null;
    }

    /**
     * Retrieve the model from string given an id .
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  integer                             $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModelResourceFromString($model, $id)
    {
        return with(new $model)->find($id);
    }

    /**
     * Handle the updated assignment's file from storage.
     *
     * @param  \Assignment\Models\Assignment $model
     * @param  array                         $attributes
     * @return void
     */
    public function handleFileFromStorage(Assignment $model, $attributes)
    {
        if (is_file($attributes['uri'] ?? false)) {
            $this->deleteFileFromStorage($model->pathname);
        }
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
            ->get()->each(function ($resource) {
                $this->deleteFileFromStorage($resource->pathname);
                $resource->forceDelete();
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
}
