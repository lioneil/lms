<?php

namespace Library\Services;

use Core\Application\Service\Concerns\CanUploadFile;
use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Library\Models\Library;
use User\Models\User;

class LibraryService extends Service implements LibraryServiceInterface
{
    use HaveAuthorization,
        CanUploadFile;

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
     * Constructor to bind model to a repository.
     *
     * @param  \Library\Models\Library  $model
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(Library $model, Request $request)
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
            'name' => 'required|max:255',
            'user_id' => 'required|numeric',
            'url' => 'required',
            'pathname' => 'required',
            'size' => 'required',
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
        $model = $this->save($model, array_merge($model->toArray(), $attributes));

        return $model->exists();
    }

    /**
     * Create or Update the passed attributes.
     *
     * @param  \Library\Models\Library $model
     * @param  array                             $attributes
     * @return \Library\Models\Library
     */
    protected function save(Library $model, array $attributes)
    {
        $model->name = $attributes['name'];
        $model->url = $attributes['url'];
        $model->pathname = $this->getPathname() ?? $attributes['pathname'] ?? $model->pathname ?? null;
        $model->size = $attributes['size'];
        $model->type = $attributes['type'] ?? $this->getTypeSignature();
        $model->user()->associate(User::find($attributes['user_id']));
        $model->save();

        return $model;
    }

    /**
     * Retrieve the storage path for assignment
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
}
