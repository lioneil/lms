<?php

namespace Menu\Services;

use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Menu\Models\Menu;

class MenuService extends Service implements MenuServiceInterface
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
     * Constructor to bind model to a repository.
     *
     * @param \Menu\Models\Menu        $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Menu $model, Request $request)
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
            'menus.*.title' => 'required|max:255',
            'menus.*.location' => 'required|max:255',
            'menus.*.uri' => 'required|max:255',
        ];
    }

    /**
     * Save the settings array to storage.
     *
     * @param  array $attributes
     * @return array
     */
    public function save(array $attributes)
    {
        $location = $attributes['location'] ?? $this->getDefaultLocation();

        foreach ($attributes['menus'] as $menu) {
            $this->updateOrCreate([
                'uri' => $menu['uri'] ?? null,
            ], [
                'title' => $menu['title'],
                'location' => $menu['location'],
                'icon' => $menu['icon'] ?? null,
                'sort' => $menu['sort'] ?? null,
                'parent' => $menu['parent'] ?? null,
                'menuable_id' => $menu['menuable_id'] ?? null,
                "menuable_type" => $menu['menuable_type'] ?? null,
                "lft" => $menu['lft'] ?? null,
                "rgt" => $menu['rgt'] ?? null,
            ]);
        }

        return $this->location($location)->get();
    }

    /**
     * Retrieve all location resources and
     * return a paginated list
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function listLocations()
    {
        if ($this->isSearching()) {
            $this->model = $this->whereIn(
                $this->getKeyName(), $this->search(
                    $this->request()->get('search')
                )->keys()->toArray()
            );
        }

        $model = $this->model->groupBy('location');

        $model = $this->model->paginate($this->getPerPage());

        $sorted = $this->sortAndOrder($model);

        return new LengthAwarePaginator($sorted, $model->total(), $model->perPage());
    }
}
