<?php

namespace Core\Console\Commands\Widgets;

use Core\Manifests\WidgetManifest;
use Illuminate\Console\Command;

class WidgetsDiscoverCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'widgets:discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild the cached widget manifest';

    /**
     * Execute the console command.
     *
     * @param  \Core\Manifests\WidgetManifest $manifest
     * @return void
     */
    public function handle(WidgetManifest $manifest)
    {
        $this->call('widgets:clear');

        $manifest->build();

        foreach ($manifest->widgets()->pluck('fullname') ?? [] as $widget) {
            $this->line("Discovered Widget: <info>{$widget}</info>");
        }

        $this->info('Widget manifest generated successfully.');
    }
}
