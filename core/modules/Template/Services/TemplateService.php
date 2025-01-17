<?php

namespace Template\Services;

use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Template\Models\Template;

class TemplateService extends Service implements TemplateServiceInterface
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
     * @param \Template\Models\Template $model
     * @param \Illuminate\Http\Request  $request
     */
    public function __construct(Template $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }
}
