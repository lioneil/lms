<?php

namespace Core\Application\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Repository implements RepositoryInterface
{
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
     * Constructor to bind model to a repository.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \Illuminate\Http\Request            $request
     */
    public function __construct(Model $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
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
     * Retrieve the request attribute.
     *
     * @return \Illuminate\Http\Request
     */
    public function request()
    {
        return $this->request;
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
    public function get():? object
    {
        return $this->model()->get();
    }

    /**
     * Retrieve model resource details.
     *
     * @param  integer $id
     * @return \Core\Models\Model
     */
    public function find(int $id):? Model
    {
        return $this->model()->findOrFail($id);
    }

    /**
     * Create model resource.
     *
     * @param  array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $attributes):? Model
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
     * @return boolean
     */
    public function delete($id)
    {
        return $this
            ->model()
            ->withTrashed()
            ->whereIn($this->model()->getKey(), (array) $id)
            ->each(function ($resource) {
                $resource->forceDelete();
            });
    }

    /**
     * Soft delete model resource.
     *
     * @param  integer|array $id
     * @return boolean
     */
    public function destroy($id)
    {
        return $this
            ->model()
            ->whereIn($this->model()->getKey(), (array) $id)
            ->each(function ($resource) {
                $resource->delete();
            });
    }

    /**
     * Restore model resource.
     *
     * @param  integer|array $id
     * @return boolean
     */
    public function restore($id)
    {
        return $this
            ->model()
            ->withTrashed()
            ->whereIn($this->model()->getKey(), (array) $id)
            ->each(function ($resource) {
                $resource->restore();
            });
    }

    /**
     * Call the model's method magically.
     *
     * @param  string $method
     * @param  array  $attributes
     * @return mixed
     */
    public function __call($method, $attributes)
    {
        return call_user_func_array([$this->model(), $method], $attributes);
    }
}
