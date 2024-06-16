<?php

namespace Core\Console\Commands\Permissions;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use User\Models\Permission;
use User\Services\PermissionServiceInterface;

class PermissionsListCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'permissions:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered permissions stored in database';

    /**
     * The PermissionService instance.
     *
     * @var \User\Services\PermissionService
     */
    protected $service;

    /**
     * Collection of permissions.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $permissions;

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = ['Name', 'Code', 'Description'];

    /**
     * The columns to display when using the "compact" flag.
     *
     * @var array
     */
    protected $compactColumns = ['code'];

    /**
     * Create a new command instance.
     *
     * @param  \User\Services\PermissionServiceInterface $service
     * @return void
     */
    public function __construct(PermissionServiceInterface $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->permissions = $this->service->all();
        } catch (\Exception $e) {
            return;
        }

        if (empty($this->permissions)) {
            return $this->error("Your application doesn't have any permissions.");
        }

        if (empty($permissions = $this->getPermissions())) {
            return $this->error("Your application doesn't have any permissions matching the given criteria.");
        }

        $this->displayPermissions($permissions);
    }

    /**
     * Compile the permissions into a displayable format.
     *
     * @return array
     */
    protected function getPermissions()
    {
        $permissions = collect($this->permissions)->map(function ($permission) {
            return $this->getPermissionInformation($permission);
        })->filter()->all();

        if ($sort = $this->option('sort')) {
            $permissions = $this->sortPermissions($sort, $permissions);
        }

        if ($this->option('reverse')) {
            $permissions = array_reverse($permissions);
        }

        return $this->pluckColumns($permissions);
    }

    /**
     * Get the model information for a given permission.
     *
     * @param  \User\Models\Permission $permission
     * @return array
     */
    protected function getPermissionInformation(Permission $permission)
    {
        return $this->filterPermission([
            'name' => $permission->name,
            'code' => $permission->code,
            'description' => $permission->description,
        ]);
    }

    /**
     * Sort the permissions by a given element.
     *
     * @param  string $sort
     * @param  array  $permissions
     * @return array
     */
    protected function sortPermissions($sort, array $permissions)
    {
        return Arr::sort($permissions, function ($permission) use ($sort) {
            return $permission[$sort];
        });
    }

    /**
     * Remove unnecessary columns from the permissions.
     *
     * @param  array $permissions
     * @return array
     */
    protected function pluckColumns(array $permissions)
    {
        return array_map(function ($permission) {
            return Arr::only($permission, $this->getColumns());
        }, $permissions);
    }

    /**
     * Filter the permission by name or code.
     *
     * @param  array $permission
     * @return array|null
     */
    protected function filterPermission(array $permission)
    {
        if (($this->option('name') && ! Str::contains($permission['name'], $this->option('name'))) ||
             $this->option('code') && ! Str::contains($permission['code'], $this->option('code'))) {
            return null;
        }

        return $permission;
    }

    /**
     * Get the table headers for the visible columns.
     *
     * @return array
     */
    protected function getHeaders()
    {
        return Arr::only($this->headers, array_keys($this->getColumns()));
    }

    /**
     * Get the column names to show (lowercase table headers).
     *
     * @return array
     */
    protected function getColumns()
    {
        $availableColumns = array_map('strtolower', $this->headers);

        if ($this->option('compact')) {
            return array_intersect($availableColumns, $this->compactColumns);
        }

        if ($columns = $this->option('columns')) {
            return array_intersect($availableColumns, $columns);
        }

        return $availableColumns;
    }

    /**
     * Display the permission information on the console.
     *
     * @param  array $permissions
     * @return void
     */
    protected function displayPermissions(array $permissions)
    {
        $this->table($this->getHeaders(), $permissions);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['columns', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Columns to include in the permission table'],

            ['compact', 'c', InputOption::VALUE_NONE, 'Only show code column'],

            ['name', null, InputOption::VALUE_OPTIONAL, 'Filter the permissions by name'],

            ['code', null, InputOption::VALUE_OPTIONAL, 'Filter the permissions by code'],

            ['reverse', 'r', InputOption::VALUE_NONE, 'Reverse the ordering of the permissions'],

            ['sort', null, InputOption::VALUE_OPTIONAL, 'The column (name, code) to sort by', 'code'],
        ];
    }
}
