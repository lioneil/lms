<?php

namespace User\Services;

use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Helpers\HaveDefaultConfig;
use Core\Application\Service\Service;
use Core\Enumerations\Role as RoleCode;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Symfony\Component\Finder\Finder;
use User\Models\Permission;
use User\Models\Role;

class RoleService extends Service implements RoleServiceInterface
{
    use HaveAuthorization,
        HaveDefaultConfig;

    /**
     * The Permission model instance.
     *
     * @var \User\Models\Permission
     */
    protected $permission;

    /**
     * Constructor to bind model to a repository.
     *
     * @param \User\Models\Role                 $model
     * @param \User\Models\Permission           $permission
     * @param \Illuminate\Http\Request          $request
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Role $model, Permission $permission, Request $request, Filesystem $files)
    {
        $this->model = $model;
        $this->permission = $permission;
        $this->request = $request;
        $this->files = $files;
    }

    /**
     * Define the validation rules for the model.
     *
     * @param  integer|null $id
     * @return array
     */
    public function rules($id = null)
    {
        return [
            'name' => 'required|max:255',
            'code' => ['required', Rule::unique($this->getTable())->ignore($id)],
            'permissions' => 'required|array',
        ];
    }

    /**
     * Retrieve the default roles
     * from configuration files.
     *
     * @return \Illuminate\Support\Collection
     */
    public function defaults()
    {
        return $this->getDefaultConfigFromFile('config/roles.php')->flatten($depth = 1);
    }

    /**
     * Import the specified resources to storage.
     *
     * @param  mixed $data Setting to true will import all defaults.
     * @return void
     */
    public function import($data = false)
    {
        $this->defaults()->filter(function ($role) use ($data) {
            return is_array($data) ? in_array($role['code'], $data['roles']) : $data;
        })->map(function ($role) {
            return $this->updateOrCreate(
                ['code' => $role['code']],
                collect($role)->except('permissions')->toArray()
            )->permissions()->sync(
                $this->permissions($role['permissions'])->pluck('id')->toArray()
            );
        });
    }

    /**
     * Save the contents of the file to database storage.
     *
     * @param  \Illuminate\Http\UploadedFile $file
     * @return void
     */
    public function upload(UploadedFile $file)
    {
        $roles = $this->toCsv(
            $this->files->getRequire($file->getPathName())
        );
    }

    /**
     * Retrieve the permissions query.
     *
     * @param  array|null $codes
     * @return mixed
     */
    protected function permissions($codes = null)
    {
        return is_null($codes) || in_array('*', $codes)
             ? $this->permission
             : $this->permission->whereIn('code', $codes);
    }

    /**
     * Create or Update the passed attributes.
     *
     * @param  \User\Models\Role $model
     * @param  array             $attributes
     * @return \User\Models\Role
     */
    protected function save($model, $attributes)
    {
        $model->name = $attributes['name'] ?? null;
        $model->code = $attributes['code'] ?? null;
        $model->alias = $attributes['alias'] ?? $attributes['name'] ?? null;
        $model->description = $attributes['description'] ?? null;
        $model->save();
        $model->permissions()->sync($attributes['permissions'] ?? []);

        return $model;
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
        $model = $this->findOrFail($id);

        $this->save($model, $attributes);

        return true;
    }
}
