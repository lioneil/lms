<?php

namespace Subscription\Services;

use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Subscription\Models\Progression;

class ProgressionService extends Service implements ProgressionServiceInterface
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
     * @param \Course\Models\Progression $model
     * @param \Illuminate\Http\Request   $request
     */
    public function __construct(Progression $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }

    /**
     * Define the validation rules for the model.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'metadata' => 'required',
            'user_id' => 'required|numeric',
        ];
    }
}
