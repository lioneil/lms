<?php

namespace Forum\Services;

use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Forum\Models\Forum;

class ForumService extends Service implements ForumServiceInterface
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
     * @param \Forum\Models\Forum      $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Forum $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }
}
