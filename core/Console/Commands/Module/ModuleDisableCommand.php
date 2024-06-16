<?php

namespace Core\Console\Commands\Module;

use Core\Manifests\ModuleManifest;
use Illuminate\Console\Command;

class ModuleDisableCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'module:disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the specified module from the manifest';

    /**
     * Execute the console command.
     *
     * @param  \Core\Manifests\ModuleManifest  $manifest
     * @return void
     */
    public function handle(ModuleManifest $manifest)
    {
        $this->qualifyModule($manifest);

        if ($module = $this->choice(
            'What module to disable?',
            $manifest->modules()->pluck('name')->toArray()
        )) {
            $this->line("Removing <info>{$module}</> from the manifest cache...");
            $manifest->remove($module);
        }

        $this->info('Module removed from manifest cache successfully.');
    }

    /**
     * Ask the user the module to enable.
     *
     * @param \Core\Manifests\ModuleManifest $manifest
     * @return void
     */
    protected function qualifyModule($manifest)
    {
        $modules = $manifest->modules();

        if ($modules->isEmpty()) {
            $this->warn('Manifest has no modules in cache. Try running the <info>module:discover</> command.');
            exit;
        }
    }
}
