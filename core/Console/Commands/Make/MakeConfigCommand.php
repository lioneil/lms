<?php

namespace Core\Console\Commands\Make;

use Core\Console\Commands\QualifyModule;
use Core\Support\Module\ModuleTrait;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeConfigCommand extends Command
{
    use ModuleTrait, QualifyModule;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:config
                           {--module= : The module the config file will belong to}
                           {--type= : The config type to generate}
                           {--name= : The name of the config keys}
                           {--force : Overwrite existing views by default}
                           ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new configuration file';

    /**
     * The type of the configuration file.
     *
     * @var array
     */
    protected $types = [
        'composers',
        'menus',
        'permissions',
        'roles',
        'sidebar',
        'widgets',
    ];

    /**
     * Create a new controller creator command instance.
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
     * @return mixed
     */
    public function handle()
    {
        $this->qualifyType();

        $this->qualifyModule();

        $this->createDirectories();

        $this->exportConfig();
    }

    /**
     * Get the type of the file to generate.
     *
     * @return string
     */
    protected function qualifyType()
    {
        if (! $this->option('type') || ! in_array($this->option('type'), $this->types)) {
            $type = $this->choice('Specify the type of the configuration file', $this->types);
        }

        $this->input->setOption('type', $type ?? $this->option('type'));

        return $this->input->getOption('type');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        $path = basename($this->getPath('config'), '.php');

        if (! is_dir($directory = $path)) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportConfig()
    {
        $type = $this->option('type');
        if (file_exists($path = $this->getPath('config/'.$type)) && ! $this->option('force')) {
            if (! $this->confirm("The [{$type}] config already exists. Do you want to replace it?")) {
                return;
            }
        }

        $replace = $this->buildReplacements();

        $stub = str_replace(
            array_keys($replace),
            array_values($replace),
            $this->files->get(stubs_path('config/'.$type.'.stub'))
        );

        $this->files->put($path, $stub);

        $this->line(trans('console.file_generated', ['name' => basename($path)]));
    }

    /**
     * Build replacements for the file.
     *
     * @return array
     */
    protected function buildReplacements()
    {
        $name = strtolower(
            ! empty($this->option('name'))
            ? $this->option('name')
            : basename($this->module['path'])
        );

        return [
            'dummy-singular-name' => str_singular($name),
            'dummy-plural-name' => str_plural($name),
            'DummyPluralText' => ucwords(str_plural($name)),
            'DummySingularText' => ucwords(str_singular($name)),
            'DummyText' => $this->option('name'),
        ];
    }
}
