<?php

namespace Core\Console\Commands\Permissions;

use Illuminate\Console\Command;
use User\Services\PermissionServiceInterface;

class RefreshPermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install permissions from config files';

    /**
     * Execute the console command.
     *
     * @param  \User\Services\PermissionServiceInterface $service
     * @return mixed
     */
    public function handle(PermissionServiceInterface $service)
    {
        $service->refresh();

        $this->call('permissions:list');

        $this->info(sprintf('   - Generated %s permissions', $service->count()));
    }
}
