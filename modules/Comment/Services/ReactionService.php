<?php

namespace Comment\Services;

use Comment\Models\Reaction;
use Core\Application\Service\Service;
use Illuminate\Http\Request;

class ReactionService extends Service implements ReactionServiceInterface
{
    /**
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
     *
     * @param \Comment\Models\Reaction $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Reaction $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }
}
