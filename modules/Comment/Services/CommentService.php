<?php

namespace Comment\Services;

use Comment\Models\Comment;
use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use User\Models\User;

class CommentService extends Service implements CommentServiceInterface
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
     * @param \Comment\Models\Comment  $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Comment $model, Request $request)
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
            'body' => 'required|max:255',
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
     * @param  \Comment\Models\Comment $model
     * @param  array                   $attributes
     * @return \Comment\Models\Comment
     */
    protected function save(Comment $model, array $attributes)
    {
        $model->body = $attributes['body'];
        $model->commentable()->associate($this->getModelResourceFromString(
            $attributes['commentable_type'], $attributes['commentable_id']
        ));
        $model->user()->associate(User::find($attributes['user_id'] ?? false) ?? Auth::user());
        $model->parent()->associate(Comment::find($attributes['parent_id'] ?? null));
        $model->locked_at = $attributes['locked_at'] ?? null;
        $model->approve();

        return $model;
    }

    /**
     * Retrieve the model from string given an id .
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  integer                             $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModelResourceFromString($model, $id)
    {
        return with(new $model)->find($id);
    }

    /**
     * Like the given resource.
     *
     * @param  \Comment\Models\Comment $comment
     * @param  array                   $attributes
     * @return \Comment\Models\Reaction
     */
    public function like(Comment $comment, $attributes)
    {
        return $comment->like(User::find($attributes['user_id']));
    }

    /**
     * Dislike the given resource.
     *
     * @param  \Comment\Models\Comment $comment
     * @param  array                   $attributes
     * @return \Comment\Models\Reaction
     */
    public function dislike(Comment $comment, $attributes)
    {
        return $comment->dislike(User::find($attributes['user_id']));
    }
}
