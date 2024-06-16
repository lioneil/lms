<?php

namespace Core\Application\Service;

use Core\Application\Permissions\RemoveApiPrefixFromPermission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

abstract class Service implements ServiceInterface
{
    use Concerns\HavePagination,
        Concerns\HaveSortOrder,
        Concerns\HaveSoftDeletes,
        Concerns\HaveOwnership,
        Concerns\SearchCapable,
        RemoveApiPrefixFromPermission;

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
     * The User model instance.
     *
     * @var \Illuminate\Auth\Authenticatable
     */
    protected $user;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table;

    /**
     * Constructor to bind model to a repository.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  \Illuminate\Http\Request            $request
     * @return void
     */
    public function __construct(Model $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
        $this->table = $model->getTable();
    }

    /**
     * Retrieve the model object.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Retrieve the model's table name.
     *
     * @return string
     */
    public function table(): string
    {
        $model = $this->model();

        if ($model instanceof Builder) {
            $model = get_class($model->getModel());
        }

        return $this->table ?? with(new $model)->getTable();
    }

    /**
     * Retrieve the model's table name.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table();
    }

    /**
     * Retrieve the request attribute.
     *
     * @return \Illuminate\Http\Request
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * The Authenticated User instance.
     *
     * @return \Illuminate\Foundation\Auth\User
     */
    public function auth()
    {
        return auth();
    }

    /**
     * Check if authenticated user is part
     * of the superadmin group.
     *
     * @return boolean
     */
    public function userIsSuperAdmin(): bool
    {
        return $this->auth()->check() && $this->auth()->user()->isSuperAdmin();
    }

    /**
     * Check if authenticated user is part
     * of the superadmin group.
     *
     * @return boolean
     */
    public function userIsUnrestricted(): bool
    {
        if (is_null($this->auth()->user())) {
            return false;
        }

        return $this->auth()->user()->isUnrestricted($this->getUnrestrictedKey());
    }

    /**
     * Retrieve the unrestricted key used by the service.
     *
     * @return string
     */
    public function getUnrestrictedKey(): string
    {
        return $this->unrestrictedKey ?? $this->getTable();
    }

    /**
     * Retrieve all model resources as array.
     *
     * @return object
     */
    public function all(): object
    {
        return $this->model()->all();
    }

    /**
     * Retrieve model collection.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get():? Collection
    {
        return $this->model()->get();
    }

    /**
     * Retrieve model resource details.
     *
     * @param  integer $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function find(int $id):? Model
    {
        return $this->model()->findOrFail($id);
    }

    /**
     * Retrieve model resource details via slug.
     *
     * @param  string $slug
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findSlug(string $slug):? Model
    {
        return $this->model()->whereSlug($slug)->firstOrFail();
    }

    /**
     * Create model resource.
     *
     * @param  array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $attributes)
    {
        return $this->model()->create($attributes);
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
        return $this->model()->find($id)->update($attributes);
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
            ->get()->each(function ($resource) {
                $resource->delete();
            });
    }

    /**
     * Restore model resource.
     *
     * @param  integer|array $id
     * @return void
     */
    public function restore($id)
    {
        $this
            ->model()
            ->withTrashed()
            ->whereIn($this->model()->getKeyName(), (array) $id)
            ->get()->each(function ($model) {
                $model->restore();
            });
    }

    /**
     * Intantiate a new Service class.
     *
     * @param  string $service
     * @return \Core\Application\Service\Service
     */
    public function service(string $service)
    {
        return App::make($service);
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
     * Call the model's method magically.
     *
     * @param  string $method
     * @param  array  $attributes
     * @return mixed
     */
    public function __call(string $method, array $attributes)
    {
        return call_user_func_array([$this->model(), $method], $attributes);
    }
}
