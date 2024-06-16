<?php

namespace Quiz\Services;

use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Quiz\Models\Quiz;
use Template\Models\Template;
use User\Models\User;

class QuizService extends Service implements QuizServiceInterface
{
    use HaveAuthorization;

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
     * The Request Instance.
     *
     * @var boolean
     */
    protected $ownable = true;

    /**
     * Constructor to bind model to a repository.
     *
     * @param \Quiz\Models\Quiz        $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Quiz $model, Request $request)
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
    public function rules($id = null): array
    {
        return [
            'title' => 'required|max:255',
            'user_id' => 'required|numeric',
        ];
    }

    /**
     * Define the validation messages for the model.
     *
     * @return array
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Create model resource.
     *
     * @param  array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $attributes)
    {
        $model = $this->model;

        return $this->save($model, $attributes);
    }

    /**
     * Update model resource.
     *
     * @param  integer $id
     * @param  array   $attributes
     * @return boolean
     */
    public function update(int $id, array $attributes): bool
    {
        $model = $this->model->findOrFail($id);
        $model = $this->save($model, array_merge($model->toArray(), $attributes));

        return $model->exists();
    }

    /**
     * Create or Update the passed attributes.
     *
     * @param  \Quiz\Models\Quiz $model
     * @param  array             $attributes
     * @return \Quiz\Models\Quiz
     */
    protected function save(Quiz $model, array $attributes)
    {
        $model->title = $attributes['title'];
        $model->subtitle = $attributes['subtitle'];
        $model->description = $attributes['description'];
        $model->slug = $attributes['slug'];
        $model->url = $attributes['url'];
        $model->method = $attributes['method'];
        $model->metadata = $attributes['metadata'];
        $model->template()->associate(Template::find($attributes['template_id']));
        $model->user()->associate(User::find($attributes['user_id']));
        $model->save();

        return $model;
    }
}
