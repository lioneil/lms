<?php

namespace Core\Console\Commands\Sidebar;

use Core\Manifests\SidebarManifest;
use Illuminate\Console\Command;

class SidebarCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sidebar:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the cached sidebar manifest';

    /**
     * Execute the console command.
     *
     * @param  \Core\Manifests\SidebarManifest $manifest
     * @return void
     */
    public function handle(SidebarManifest $manifest)
    {
        $this->call('sidebar:clear');

        $manifest->build();

        foreach ($manifest->all()->pluck('name') ?? [] as $name) {
            $this->line("Discovered sidebar menu: <info>{$name}</info>");
        }

        $this->info('Sidebar manifest generated successfully.');
    }
}
