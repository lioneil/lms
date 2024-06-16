<?php

namespace Core\Console\Commands\Module;

use Core\Console\Commands\QualifyModule;
use Core\Manifests\ModuleManifest;
use Core\Support\Module\ModuleTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ModuleViewsCommand extends Command
{
    use ModuleTrait, QualifyModule;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'module:views
                    {--module : Scaffold the views to a module}
                    {--category : Generate views extending the Category module}
                    {--force : Overwrite existing views by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate scaffold view blade files for a module';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'category' => [
            'categories/edit.stub' => 'categories/edit.blade',
            'categories/index.stub' => 'categories/index.blade',
        ],
        'admin' => [
            'admin/create.stub' => 'admin/create.blade',
            'admin/edit.stub' => 'admin/edit.blade',
            'admin/index.stub' => 'admin/index.blade',
            'admin/show.stub' => 'admin/show.blade',
            'admin/trashed.stub' => 'admin/trashed.blade',
        ],
    ];

    /**
     * Execute the console command.
     *
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->qualifyModule();

        $this->createDirectories();

        $this->exportViews();
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        $path = 'views/'.($this->option('category') ? 'categories' : 'admin');

        if (! is_dir($directory = $this->getPath($path, $withExtension = false))) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views[
            $this->option('category') ? 'category' : 'admin'
        ] as $key => $value) {
            if (file_exists($view = $this->getPath('views/'.$value)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                stubs_path('make/views/'.$key),
                $view
            );

            $this->line('   - Generated file: <info>'.basename($view).'</>');
        }
    }
}
