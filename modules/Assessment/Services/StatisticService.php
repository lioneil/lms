<?php

namespace Assessment\Services;

use Assessment\Models\Assessment;
use Assessment\Models\Statistic;
use Core\Application\Service\Service;
use Illuminate\Http\Request;

class StatisticService extends Service implements StatisticServiceInterface
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
     * @param \Assessment\Models\Statistic $model
     * @param \Illuminate\Http\Request     $request
     */
    public function __construct(Statistic $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }
}
