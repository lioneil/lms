<?php

namespace Core\Console\Commands\Sidebar;

use Core\Application\Sidebar\Contracts\SidebarInterface;
use Core\Manifests\SidebarManifest;
use Illuminate\Console\Command;

class SidebarListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sidebar:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered sidebar menus';

    /**
     * The Sidebar instance.
     *
     * @var \Core\Application\Sidebar\Contracts\SidebarInterface
     */
    protected $sidebar;

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = ['Module', 'Name', 'URI'];

    /**
     * Create a new command instance.
     *
     * @param  \Core\Application\Sidebar\Contracts\SidebarInterface $sidebar
     * @return void
     */
    public function __construct(SidebarInterface $sidebar)
    {
        parent::__construct();

        $this->sidebar = $sidebar;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->callSilent('sidebar:cache');

        $this->render();
    }

    /**
     * Print the sidebar menu to console.
     *
     * @param  array|null $items
     * @param  integer    $i
     * @return void
     */
    protected function render($items = null, int $i = 1)
    {
        foreach ($items ?? $this->sidebar->all() as $name => $menu) {
            $line = str_repeat('-', $i);
            if ($menu->is('header')) {
                $this->warn(" $line =={$menu->text()}==");
            } else {
                $this->info(" $line {$menu->text()}");
            }

            if ($menu->hasChild()) {
                $this->render($menu->children(), $i+3);
            }
        }
    }
}
