<?php

namespace Core\Console\Commands\Theme;

use Core\Manifests\ThemeManifest;
use Illuminate\Console\Command;

class ThemeActivateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:activate
                           {theme=none : The theme to activate}
                           {--user : Only activate the theme for a specific user}
                           ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate site theme from the theme manifest selection';

    /**
     * Execute the console command.
     *
     * @param \Core\Manifests\ThemeManifest $manifest
     * @return mixed
     */
    public function handle(ThemeManifest $manifest)
    {
        if (($theme = $this->argument('theme')) == 'none') {
            $theme = $this->choice('Pick the theme to activate', $manifest->themes()->pluck('name', 'code')->toArray());
        }

        if ($manifest->activate($theme)) {
            $this->line("Theme <info>{$theme}</> is activated");
        }

        settings(['app:theme' => $theme])->save();
        $this->line("Theme <info>{$theme}</> saved to settings");
    }
}
