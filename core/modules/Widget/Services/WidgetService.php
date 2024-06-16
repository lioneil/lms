<?php

namespace Widget\Services;

use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Core\Manifests\WidgetManifest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use User\Models\Role;
use Widget\Models\Widget;

class WidgetService extends Service implements WidgetServiceInterface
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
     * The Request Instance.
     *
     * @var boolean
     */
    protected $ownable = false;

    /**
     * The WidgetManifest instance.
     *
     * @var \Core\Manifests\WidgetManifest
     */
    protected $manifest;

    /**
     * Constructor to bind model to a repository.
     *
     * @param  \Widget\Models\Widget          $model
     * @param  \Core\Manifests\WidgetManifest $manifest
     * @param  \Illuminate\Http\Request       $request
     * @return void
     */
    public function __construct(Widget $model, WidgetManifest $manifest, Request $request)
    {
        $this->model = $model;
        $this->manifest = $manifest;
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
            'file' => 'required|max:255',
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
     * Retrieve the default widgets
     * from configuration files.
     *
     * @return \Illuminate\Support\Collection
     */
    public function defaults()
    {
        return $this->manifest->all();
    }

    /**
     * Retrieve the aliased widget from manifest.
     *
     * @param  string $alias
     * @return mixed
     */
    public function alias(string $alias)
    {
        return $this->manifest->find($alias);
    }

    /**
     * Refresh the discovered widgets.
     *
     * @return null
     */
    public function refresh()
    {
        return Artisan::call('widgets:discover');
    }

    /**
     * Retrieve the all widgets registered in the WidgetManifest.
     *
     * @return array
     */
    public function widgets()
    {
        return $this->manifest->all()->map(function ($w) {
            $widget = app()->make($w['fullname']);
            return [
                'name' => $w['name'],
                'alias' => $w['alias'],
                'data' => $widget->attributes(),
                'description' => $w['description'],
                'interval' => $widget->getIntervals(),
                'render' => $widget->render($widget->attributes()),
            ];
        });
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
        $model = $this->save($model, $attributes);

        return $model->exists();
    }

    /**
     * Create or Update the passed attributes.
     *
     * @param  \Widget\Models\Widget $model
     * @param  array                 $attributes
     * @return \Widget\Models\Widget
     */
    public function save(Widget $model, $attributes)
    {
        $model->file = $attributes['file'];
        $model->namespace = $attributes['namespace'];
        $model->fullname = $attributes['fullname'];
        $model->name = $attributes['name'];
        $model->alias = $attributes['alias'];
        $model->description = $attributes['description'];
        $model->save();

        // Widget roles.
        $model->roles()->sync($attributes['roles'] ?? []);

        return $model;
    }

    /**
     * Retrieve all widget resources and
     * return a paginated list
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function listByRole()
    {
        if ($this->isSearching()) {
            $this->model = $this->whereIn(
                $this->getKeyName(), $this->search(
                    $this->request()->get('search')
                )->keys()->toArray()
            );
        }

        $model = $this->model->whereHas('roles', function ($query) {
            $query->whereIn('id', $this->request()->get('roles') ?: []);
        });

        $model = $model->paginate($this->getPerPage());

        $sorted = $this->sortAndOrder($model);

        return new LengthAwarePaginator($sorted, $model->total(), $model->perPage());
    }
}
