<?php

namespace Core\Console\Commands\Optimize;

use Core\Support\Module\ModuleTrait;
use Illuminate\Foundation\Console\ViewCacheCommand as BaseCommand;

class ViewCacheCommand extends BaseCommand
{
    use ModuleTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'view:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Compile all of the application's Blade templates";

    /**
     * Get all of the possible view paths.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function paths()
    {
        $finder = $this->laravel['view']->getFinder();
        $modules = $this->modules()->reject(function ($module) {
            return ! is_dir("{$module['path']}/views");
        })->map(function ($module) {
            return "{$module['path']}/views";
        });

        return $modules->merge(collect($finder->getPaths())->merge(
            collect($finder->getHints())->flatten()
        ));
    }
}
