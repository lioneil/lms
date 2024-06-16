<?php

namespace User\Services;

use Core\Application\Service\Concerns\CanUploadFile;
use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Core\Enumerations\DetailType;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use User\Models\User;

class UserService extends Service implements UserServiceInterface
{
    use Concerns\SavesAccountRecord,
        CanUploadFile,
        HaveAuthorization;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $appendToList = ['roles'];

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Constructor to bind model to a repository.
     *
     * @param \User\Models\User        $model
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
            'roles' => 'required',
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
     * Create or Update the passed attributes.
     *
     * @param  \User\Models\User $model
     * @param  array             $attributes
     * @return \User\Models\User
     */
    protected function save($model, $attributes)
    {
        $model->prefixname = $attributes['prefixname'];
        $model->firstname = $attributes['firstname'];
        $model->middlename = $attributes['middlename'];
        $model->lastname = $attributes['lastname'];
        $model->suffixname = $attributes['suffixname'];
        $model->username = $attributes['username'] ?? $model->username;
        $model->email = $attributes['email'] ?? $model->email;
        $model->password = $attributes['password'] ?? false ? $this->hash($attributes['password']) : $model->password;
        $model->type = $attributes['type'] ?? $model->type;
        $model->save();

        // Move to observer class.
        $model->photo = $attributes['photo'] ?? false
            ? $this->upload($attributes['photo'], $model->getKey())
            : $attributes['avatar'] ?? null;
        $model->save();

        // User roles.
        $model->roles()->sync($attributes['roles'] ?? []);

        // User details.
        $details = collect($attributes['details'] ?? [])->each(function ($detail) use ($model) {
            if (! empty($detail['key'])) {
                return $model->details()->updateOrCreate([
                    'key' => $detail['key'],
                    'type' => DetailType::DETAIL,
                ], [
                    'icon' => $detail['icon'] ?? DetailType::DEFAULT_ICON,
                    'key' => $detail['key'] ?? null,
                    'value' => $detail['value'] ?? null,
                ]);
            }
        });

        $model->details()
            ->where('type', DetailType::DETAIL)
            ->whereNotIn('key', $details->pluck('key'))
            ->delete();

        return $model;
    }

    /**
     * Upload the given file.
     *
     * @param  \Illuminate\Http\UploadedFile $file
     * @param  string                        $folder
     * @return string|null
     */
    public function upload(UploadedFile $file, $folder = null)
    {
        $folderName = settings(
            'storage:modules', 'modules/'.$this->getTable()
        ).DIRECTORY_SEPARATOR.'avatars'.DIRECTORY_SEPARATOR.$folder;

        $uploadPath = storage_path($folderName);

        $storage = new Filesystem;
        $storage->cleanDirectory($uploadPath);

        $fileName = 'avatar-'.$folder.'.'.$file->getClientOriginalExtension();
        $fullFilePath = "$uploadPath/$fileName";

        if ($file->move($uploadPath, $fileName)) {
            return url("storage/$folderName/$fileName");
        }

        return null;
    }
}
