<?php

namespace Setting\Services;

use Core\Application\Service\Concerns\HaveAuthorization;
use Core\Application\Service\Service;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Setting\Enumerations\SettingsKey;
use Setting\Models\Setting;
use Setting\Services\SettingService;
use Setting\Services\SettingServiceInterface;
use User\Models\User;

class SettingService extends ConfigRepository implements SettingServiceInterface
{
    use HaveAuthorization;

    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * The key column field.
     *
     * @var string
     */
    protected $key = 'key';

    /**
     * The value column field.
     *
     * @var string
     */
    protected $value = 'value';

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
    protected $table = 'settings';

    /**
     * The status value from database.
     *
     * @var integer
     */
    protected $status = 1;

    /**
     * Create a new configuration repository.
     *
     * @param  \Setting\Models\Setting  $model
     * @param  \Illuminate\Http\Request $request
     * @param  array                    $items
     * @return void
     */
    public function __construct(Setting $model, Request $request, array $items = [])
    {
        $this->model = $model;
        $this->request = $request;
        $this->merge($items);
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
     * Retrieve the full model instance.
     *
     * @return \Pluma\Models\Model
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Retrieve the key column field.
     *
     * @return string
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Retrieve the value column field.
     *
     * @return string
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string $key
     * @param  mixed        $value
     * @return \Setting\Repositories\SettingRepository
     */
    public function set($key, $value = null)
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $key => $value) {
            Arr::set($this->items, $key, $value);
        }

        return $this;
    }

    /**
     * Create model resource.
     *
     * @param  array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store(array $attributes)
    {
        $this->items = $attributes;

        return $this->save();
    }

    /**
     * Save the settings array to storage.
     *
     * @return void
     */
    public function save()
    {
        foreach ($this->all() as $key => $value) {
            $this->updateOrCreate([
                $this->key() => $key
            ], [
                $this->value() => $value,
                'user_id' => Auth::check() ? Auth::user()->getKey() : null,
            ]);
        }
    }

    /**
     * Upload the app:logo value.
     *
     * @param  array  $attributes
     * @param  string $key
     * @param  string $name
     * @return string|boolean
     */
    public function upload(array $attributes, $key = SettingsKey::APP_LOGO, $name = SettingsKey::LOGO_NAME)
    {
        if (collect($attributes)->has($key)) {
            $file = $attributes[$key];
            $fileName = $name.'.'.$file->getClientOriginalExtension();
            $uploadPath = public_path();

            if ($file->move($uploadPath, $fileName)) {
                $this->store([$key => url($fileName)]);

                return url($fileName);
            }
        }

        return false;
    }

    /**
     * Merge items from database
     * and user defined on runtime.
     *
     * @param  array $items
     * @return void
     */
    public function merge(array $items)
    {
        $this->items = Collection::make($items)->merge(
            $this->model()
            ->whereStatus($this->status)
            ->pluck($this->value, $this->key)
            ->toArray()
        );
    }

    /**
     * @param  string $method
     * @param  array  $attributes
     * @return mixed
     */
    public function __call(string $method, array $attributes)
    {
        return call_user_func_array([$this->model(), $method], $attributes);
    }

    /**
     * Retrieve the user's settings.
     *
     * @param  \User\Models\User $user
     * @return \Illuminate\Support\Collection
     */
    public function getSettingsForUser(User $user = null)
    {
        $user = $user ?: Auth::user();

        return $user && $user->settings->mapWithKeys(function ($item, $key) {
            return [$key => $item];
        });
    }

    /**
     * Retrieve the item with the given key.
     *
     * @param  string $attribute
     * @return mixed
     */
    public function containsKey($attribute)
    {
        return $this->all()->filter(function ($item, $k) use ($attribute) {
            return Str::contains($k, $attribute);
        })->mapWithKeys(function ($item, $key) {
            return [$key => $item];
        });
    }

    /**
     * Retrieve the request attribute.
     *
     * @return \Illuminate\Http\Request
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * Retrieve all resources and paginate.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function list()
    {
        return $this->containsKey($this->request()->get('key') ?: 'app');
    }
}
