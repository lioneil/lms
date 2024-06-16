<?php

namespace Test\Services;

use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Test\Models\Test;

class TestService extends Service implements TestServiceInterface
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
     * @param \Test\Models\Test        $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Test $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }
}
