<?php

namespace Core\Console\Commands\Theme;

use Core\Manifests\ThemeManifest;
use Illuminate\Console\Command;

class ThemeDiscoverCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild the cached theme manifest';

    /**
     * Execute the console command.
     *
     * @param  \Core\Manifests\ThemeManifest  $manifest
     * @return void
     */
    public function handle(ThemeManifest $manifest)
    {
        $manifest->build();

        foreach ($manifest->themes()->pluck('name') ?? [] as $theme) {
            $this->line("Discovered Theme: <info>{$theme}</info>");
        }

        $this->info('Theme manifest generated successfully.');
    }
}
