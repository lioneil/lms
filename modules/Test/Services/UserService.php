<?php

namespace Test\Services;

use Core\Application\Service\Concerns\CanUploadFile;
use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Core\Enumerations\Role as RoleCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Test\Models\User;

class UserService extends Service implements UserServiceInterface
{
    use CanUploadFile,
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
     * Constructor to bind model to a repository.
     *
     * @param \Test\Models\User        $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(User $model, Request $request)
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
    public function rules($id = null)
    {
        return [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'email' => ['required', 'email', Rule::unique($this->getTable())->ignore($id)],
            'username' => ['required', Rule::unique($this->getTable())->ignore($id)],
            'password' => 'sometimes|required|min:6',
        ];
    }

    /**
     * Generate random hash key.
     *
     * @param  string $key
     * @return string
     */
    public function hash(string $key)
    {
        return Hash::make($key);
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
        $user = $this->save($model, $attributes);

        return $user;
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
        $model->prefixname = $attributes['prefixname'] ?? null;
        $model->firstname = $attributes['firstname'];
        $model->middlename = $attributes['middlename'] ?? null;
        $model->lastname = $attributes['lastname'];
        $model->suffixname = $attributes['suffixname'] ?? null;
        $model->username = $attributes['username'];
        $model->email = $attributes['email'];
        $model->photo = $this->handlePhotoUpload($attributes);
        $model->password = $attributes['password'] ?? false ? $this->hash($attributes['password']) : $model->password;
        $model->type = RoleCode::TEST;
        $model->save();

        return $model;
    }

    /**
     * Retrieve or upload image.
     *
     * @param  array $attributes
     * @return string
     */
    protected function handlePhotoUpload($attributes)
    {
        return is_file($attributes['photo'] ?? false)
            ? $this->upload($attributes['photo'])
            : $attributes['photo_old'] ?? $attributes['photo'] ?? null;
    }
}
