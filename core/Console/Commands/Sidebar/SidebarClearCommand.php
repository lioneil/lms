<?php

namespace Core\Console\Commands\Sidebar;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class SidebarClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sidebar:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the sidebar manifest cache file';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new route clear command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->files->delete($this->laravel->getCachedSidebarPath());

        $this->info('Sidebar manifest cache cleared!');
    }
}
