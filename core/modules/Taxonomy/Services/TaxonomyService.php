<?php

namespace Taxonomy\Services;

use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Taxonomy\Models\Taxonomy;

class TaxonomyService extends Service implements TaxonomyServiceInterface
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
     * Constructor to bind model to a repository.
     *
     * @param \Taxonomy\Models\Taxonomy $model
     * @param \Illuminate\Http\Request  $request
     */
    public function __construct(Taxonomy $model, Request $request)
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
    public function rules($id = null)
    {
        return [
            'name' => 'required|max:255',
            'alias' => 'required|max:255',
            'code' => ['required', 'max:255', Rule::unique($this->getTable())->ignore($id)],
            'type' => 'required|max:255',
        ];
    }
}
