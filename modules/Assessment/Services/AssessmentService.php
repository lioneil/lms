<?php

namespace Assessment\Services;

use Assessment\Exports\AssessmentsExport;
use Assessment\Imports\AssessmentsImport;
use Assessment\Models\Assessment;
use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Template\Models\Template;
use User\Models\User;

class AssessmentService extends Service implements AssessmentServiceInterface
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
     * Property to check if model is ownable.
     *
     * @var boolean
     */
    protected $ownable = true;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'forms';

    /**
     * Constructor to bind model to a repository.
     *
     * @param \Assessment\Models\Assessment $model
     * @param \Illuminate\Http\Request      $request
     */
    public function __construct(Assessment $model, Request $request)
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
            'subtitle' => 'required|max:255',
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
        $model = $this->model->findOrFail($id);
        $model = $this->save($model, array_merge($model->toArray(), $attributes));

        return $model->exists();
    }

    /**
     * Create or Update the passed attributes.
     *
     * @param  \Assessment\Models\Assessment $model
     * @param  array                         $attributes
     * @return \Assessment\Models\Assessment
     */
    protected function save(Assessment $model, array $attributes)
    {
        $model->title = $attributes['title'];
        $model->subtitle = $attributes['subtitle'];
        $model->description = $attributes['description'];
        $model->slug = Str::slug($attributes['slug']);
        $model->url = $attributes['url'];
        $model->method = $attributes['method'];
        $model->type = $attributes['type'];
        $model->metadata = $attributes['metadata'] ?? null;
        $model->template()->associate(Template::find($attributes['template_id'] ?? null));
        $model->user()->associate(User::find($attributes['user_id']));
        $model->save();

        return $model;
    }

    /**
     * Export a resource or resources to a human-readable
     * format. E.g. PDF, Spreadsheet, CSV, etc.
     *
     * @param  array  $attributes
     * @param  string $format
     * @param  string $filename
     * @return mixed
     */
    public function export(array $attributes, string $format, string $filename = null)
    {
        $assessments = $this->whereIn('id', $attributes['id'])->get();

        return new AssessmentsExport($assessments, $filename, $format);
    }

    /**
     * Import from array, file, or any resource.
     *
     * @param  array|mixed $file
     * @return void
     */
    public function import($file)
    {
        with(new AssessmentsImport)->import($file);
    }
}
