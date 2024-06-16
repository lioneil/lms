<?php

namespace Assessment\Services;

use Assessment\Models\Assessment;
use Assessment\Models\Field;
use Assessment\Services\Concerns\ExportableExaminee;
use Core\Application\Service\Concerns\CanUploadFile;
use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FieldService extends Service implements FieldServiceInterface
{
    use Concerns\ExportableExaminee,
        CanUploadFile,
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
     * Property to check if model is ownable.
     *
     * @var boolean
     */
    protected $ownable = false;

    /**
     * Constructor to bind model to a repository.
     *
     * @param \Assessment\Models\Field $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Field $model, Request $request)
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
            'code' => 'required|max:255',
            'form_id' => 'required|numeric',
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
     * @param  \Assessment\Models\Field $model
     * @param  array                    $attributes
     * @return \Assessment\Models\Field
     */
    protected function save(Field $model, array $attributes)
    {
        $model->title = $attributes['title'];
        $model->code = $attributes['code'];
        $model->type = $attributes['type'];
        $model->metadata = $attributes['metadata'];
        $model->form_id = $attributes['form_id'];
        $model->group = $attributes['group'];
        $model->sort = $attributes['sort'] ?? null;
        $model->save();

        return $model;
    }

    /**
     * Validate and sanitize code.
     *
     * @param  string  $code
     * @param  integer $fieldId
     * @param  integer $i
     * @return string
     */
    public function handleCode($code, $fieldId, $i = 0)
    {
        $text = $code;

        if ($this->whereCode($text)->whereIn('form_id', [$fieldId])->exists()) {
            do {
                $text = sprintf('%s-%s', Str::slug($code), ++$i);
            } while ($this->whereCode($text)->exists());
        }

        return $text;
    }


    /**
     * Clone model resource
     *
     * @param  integer $id
     * @param  array   $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function clone($id, $attributes = [])
    {
        $field = $this->findOrFail($id);
        $model = $field->replicate();
        $model->title = sprintf('Clone of %s', $model->title);
        $model->code = $this->handleCode(
            $model->code, $model->form_id ?? $attributes['form_id'] ?? null
        );
        $model->metadata = $this->handleMetadata(array_merge(
            is_null($model->metadata) ? [] : $model->metadata->toArray(),
            $attributes['metadata'] ?? []
        ));
        $model->save();

        $model->assessment->fields->each(function ($field, $i) {
            $field->sort = $sort = $i + 1;
            $field->save();
        });

        return $model;
    }

    /**
     * Validate and sanitize metadata.
     *
     * @param  array $attributes
     * @return array
     */
    public function handleMetadata($attributes)
    {
        return array_merge($attributes ?? [], ['pathname' => $this->getPathname()]);
    }

    /**
     * Reorder the sort column of the contents.
     *
     * @param  array $attributes
     * @return boolean
     */
    public function reorder($attributes)
    {
        collect($attributes['fields'])->each(function ($item) {
            $field = $this->find($item['id']);
            $field->sort = $item['sort'];
            $field->metadata = $this->handleMetadata(array_merge(
                is_null($field->metadata) ? [] : $field->metadata->toArray(),
                $item['metadata'] ?? []
            ));
            $field->reorder();
        });

        return true;
    }
}
