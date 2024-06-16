<?php

namespace Core\Console\Commands\Permissions;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use User\Models\Role;
use User\Services\RoleServiceInterface;

class RolesListCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'roles:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered roles stored in database';

    /**
     * The RoleService instance.
     *
     * @var \User\Services\RoleService
     */
    protected $service;

    /**
     * Collection of roles.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $roles;

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = ['Name', 'Code', 'Description', 'Permissions'];

    /**
     * The columns to display when using the "compact" flag.
     *
     * @var array
     */
    protected $compactColumns = ['name', 'code', 'permissions'];

    /**
     * Create a new command instance.
     *
     * @param  \User\Services\RoleServiceInterface $service
     * @return void
     */
    public function __construct(RoleServiceInterface $service)
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
            $this->roles = $this->service->all();
        } catch (\Exception $e) {
            return;
        }

        if (empty($this->roles)) {
            return $this->error("Your application doesn't have any roles.");
        }

        if (empty($roles = $this->getRoles())) {
            return $this->error("Your application doesn't have any roles matching the given criteria.");
        }

        $this->displayRoles($roles);
    }

    /**
     * Compile the roles into a displayable format.
     *
     * @return array
     */
    protected function getRoles()
    {
        $roles = collect($this->roles)->map(function ($role) {
            return $this->getRoleInformation($role);
        })->filter()->all();

        if ($sort = $this->option('sort')) {
            $roles = $this->sortRoles($sort, $roles);
        }

        if ($this->option('reverse')) {
            $roles = array_reverse($roles);
        }

        return $this->pluckColumns($roles);
    }

    /**
     * Get the model information for a given role.
     *
     * @param  \User\Models\Role $role
     * @return array
     */
    protected function getRoleInformation(Role $role)
    {
        return $this->filterRole([
            'name' => $role->name,
            'code' => $role->code,
            'description' => $role->description,
            'permissions' => $role->permissions->implode('code', PHP_EOL),
        ]);
    }

    /**
     * Sort the roles by a given element.
     *
     * @param  string $sort
     * @param  array  $roles
     * @return array
     */
    protected function sortRoles($sort, array $roles)
    {
        return Arr::sort($roles, function ($role) use ($sort) {
            return $role[$sort];
        });
    }

    /**
     * Remove unnecessary columns from the roles.
     *
     * @param  array $roles
     * @return array
     */
    protected function pluckColumns(array $roles)
    {
        return array_map(function ($role) {
            return Arr::only($role, $this->getColumns());
        }, $roles);
    }

    /**
     * Filter the role by name or code.
     *
     * @param  array $role
     * @return array|null
     */
    protected function filterRole(array $role)
    {
        if (($this->option('name') && ! Str::contains($role['name'], $this->option('name'))) ||
             $this->option('code') && ! Str::contains($role['code'], $this->option('code'))) {
            return null;
        }

        return $role;
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
     * Display the role information on the console.
     *
     * @param  array $roles
     * @return void
     */
    protected function displayRoles(array $roles)
    {
        $this->table($this->getHeaders(), $roles);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['columns', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Columns to include in the role table'],

            ['compact', 'c', InputOption::VALUE_NONE, 'Only show code column'],

            ['name', null, InputOption::VALUE_OPTIONAL, 'Filter the roles by name'],

            ['code', null, InputOption::VALUE_OPTIONAL, 'Filter the roles by code'],

            ['reverse', 'r', InputOption::VALUE_NONE, 'Reverse the ordering of the roles'],

            ['sort', null, InputOption::VALUE_OPTIONAL, 'The column (name, code) to sort by', 'code'],
        ];
    }
}
