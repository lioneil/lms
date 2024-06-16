<?php

namespace Subscription\Services;

use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Subscription\Models\Subscription;

class SubscriptionService extends Service implements SubscriptionServiceInterface
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
     * @param \Subscription\Models\Subscription $model
     * @param \Illuminate\Http\Request          $request
     */
    public function __construct(Subscription $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }
}
