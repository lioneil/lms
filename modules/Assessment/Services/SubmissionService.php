<?php

namespace Assessment\Services;

use Assessment\Models\Submission;
use Assessment\Services\Concerns\ExportableSubmission;
use Core\Application\Service\Concerns\CanUploadFile;
use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Illuminate\Http\Request;
use User\Models\User;

class SubmissionService extends Service implements SubmissionServiceInterface
{
    use CanUploadFile,
        Concerns\ExportableSubmission,
        HaveAuthorization;

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
     * Property to check if model is ownable
     *
     * @var boolean
     */
    protected $ownable = true;

    /**
     * The table name
     *
     * @var string
     */
    protected $table = 'submissions';

    /**
     * Constructor to bind model to a repository.
     *
     * @param \Assessment\Models\Submission $model
     * @param \Illuminate\Http\Request      $request
     */
    public function __construct(Submission $model, Request $request)
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
            'results' => 'required|max:255',
            'submissible_id' => 'required|numeric',
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
        $model = $this->model();

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
        $model = $this->model()->findOrFail($id);
        $model = $this->save($model, array_merge($model->toArray(), $attributes));

        return $model->exists();
    }

    /**
     * Update or Save new resource to storage.
     *
     * @param  \Assessment\Models\Submission $model
     * @param  array                         $attributes
     * @return \Assessment\Models\Submission
     */
    public function save(Submission $model, array $attributes)
    {
        $model->results = $attributes['results'];
        $model->remarks = $attributes['remarks'] ?? null;
        $model->metadata = $attributes['metadata'] ?? null;
        $model->submissible()->associate($this->getModelResourceFromString(
            $attributes['submissible_type'], $attributes['submissible_id']
        ));
        $model->user()->associate(User::find($attributes['user_id']));
        $model->save();

        return $model;
    }

    /**
     * Permanently delete model resource.
     *
     * @param  integer|array $id
     * @return void
     */
    public function delete($id)
    {
        $this
            ->model()
            ->withTrashed()
            ->whereIn($this->model()->getKeyName(), (array) $id)
            ->get()->each(function ($model) {
                $this->deleteFileFromStorage(
                    is_null($model->metadata) ? false : $model->metadata->get('pathname')
                );
                $model->forceDelete();
            });
    }

    /**
     * Soft delete model resource.
     *
     * @param  integer|array $id
     * @return void
     */
    public function destroy($id)
    {
        $this
            ->model()
            ->whereIn($this->model()->getKeyname(), (array) $id)
            ->get()->each(function ($model) {
                $model->delete();
            });
    }

    /**
     * Retrieve the model from string given an id.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  integer                             $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModelResourceFromString($model, $id)
    {
        return with(new $model)->find($id);
    }
}
