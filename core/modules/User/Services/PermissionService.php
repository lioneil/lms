<?php

namespace User\Services;

use Core\Application\Service\Helpers\HaveDefaultConfig;
use Core\Application\Service\Service;
use Core\Support\Facades\ModuleManifest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use User\Models\Permission;

class PermissionService extends Service implements PermissionServiceInterface
{
    use HaveDefaultConfig;

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
     * @param \User\Models\Permission  $permission
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Permission $permission, Request $request)
    {
        $this->model = $permission;

        $this->request = $request;
    }

    /**
     * Retrieve all permissions
     * from config/permissions.php
     *
     * @return \Illuminate\Support\Collection
     */
    public function permissions()
    {
        return $this->getDefaultConfigFromFile('config/permissions.php');
    }

    /**
     * Update existing or create new permissions
     * from file.
     *
     * @return void
     */
    public function refresh()
    {
        $this->permissions()->flatten($depth = 1)->each(function ($permission) {
            $this->model()->updateOrCreate(['code' => $permission['code']], $permission);
        });

        $this->model()->refresh();
    }

    /**
     * Truncate and repopulate the permissions table.
     *
     * @return void
     */
    public function reset()
    {
        Schema::disableForeignKeyConstraints();
        DB::table($this->getTable())->truncate();
        Schema::enableForeignKeyConstraints();

        $this->refresh();
    }

    /**
     * Retrieved the mapped permissions list.
     *
     * @return array
     */
    public function grouped()
    {
        return $this->get()->groupBy('group')->map(function ($permissions, $key) {
            return [
                'id' => $id = $key.time(),
                'key' => $key,
                'name' => ucfirst($key),
                'order' => $id,
                'children' => $permissions->map(function ($permission, $i) {
                    return array_merge($permission->toArray(), [
                        'name' => $permission->code,
                        'key' => $permission->code,
                        'order' => $i,
                    ]);
                })->toArray(),
            ];
        });
    }
}
