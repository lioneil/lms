<?php

namespace Classroom\Services;

use Classroom\Models\Classroom;
use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Course\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use User\Models\User;

class ClassroomService extends Service implements ClassroomServiceInterface
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
     * The table name.
     *
     * @var string
     */
    protected $table = 'classrooms';

    /**
     * Constructor to bind model to a repository.
     *
     * @param \Classroom\Models\Classroom $model
     * @param \Illuminate\Http\Request    $request
     */
    public function __construct(Classroom $model, Request $request)
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
            'name' => 'required|max:255',
            'user_id' => 'required|numeric',
            'courses' => 'required|array',
            'code' => ['required', 'regex:/[a-zA-Z0-9\s]+/', Rule::unique($this->getTable())->ignore($id)],
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
        $model = $this->save($model, $attributes);

        return $model->exists();
    }

    /**
     * Create or Update the passed attributes.
     *
     * @param  \User\Models\User $model
     * @param  array             $attributes
     * @return \User\Models\User
     */
    protected function save($model, $attributes)
    {
        $model->name = $attributes['name'];
        $model->code = $attributes['code'];
        $model->description = $attributes['description'] ?? null;
        $model->user()->associate(User::find($attributes['user_id']));
        $model->save();

        // Course.
        $model->courses()->sync($attributes['courses'] ?? []);

        // Student.
        $model->students()->sync($attributes['students'] ?? []);

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
            ->whereIn($this->model()->getKeyName(), (array) $id)
            ->get()->each(function ($model) {
                $model->delete();
            });
    }

    /**
     * Attach model resource.
     *
     * @param  \Classroom\Models\Classroom $classroom
     * @param  array                       $attributes
     * @return \Classroom\Models\Classroom
     */
    public function attach(Classroom $classroom, $attributes)
    {
        $classroom->courses()->attach($attributes['course_id']);

        return $classroom;
    }

    /**
     * Detach model resource.
     *
     * @param  \Classroom\Models\Classroom $classroom
     * @param  array                       $attributes
     * @return \Classroom\Models\Classroom
     */
    public function detach(Classroom $classroom, $attributes)
    {
        $classroom->courses()->detach($attributes['course_id']);

        return $classroom;
    }
}
