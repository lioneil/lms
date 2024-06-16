<?php

namespace Course\Services;

use Core\Application\Service\Service;
use Course\Models\Lesson;
use Illuminate\Http\Request;

class LessonService extends Service implements LessonServiceInterface
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
     * @param \Course\Models\Lesson    $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Lesson $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }
}
