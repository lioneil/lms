<?php

namespace Mail\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Mail\Models\Mail;

class MailService extends Collection implements MailServiceInterface
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
     * @param  \Mail\Models\Mail        $model
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(Mail $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
        $this->items = ['ad', 'asd'];
    }

    /**
     * Retrieve all resources and paginate.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function list()
    {
        return $this->model->all();
    }
}
