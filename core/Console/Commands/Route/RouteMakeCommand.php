<?php

namespace Core\Console\Commands\Route;

use Core\Console\Commands\QualifyModule;
use Core\Support\Module\ModuleTrait;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class RouteMakeCommand extends Command
{
    use ModuleTrait, QualifyModule;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:route
                           {--module= : The module the config file will belong to}
                           {--type= : The type of route to generate}
                           {--admin : Generate an admin route}
                           {--web : Generate a public route}
                           {--api : Generate an API route}
                           {--force : Overwrite existing route by default}
                           ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate route file for a given module';

    /**
     * The type of the configuration file.
     *
     * @var array
     */
    protected $types = [
        'admin',
        'api',
        'assets',
        'channels',
        'console',
        'web',
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

        $this->createRouteDirectoryifNotExists();

        $this->generateRouteFile();
    }

    /**
     * Get the type of the file to generate.
     *
     * @return string
     */
    protected function qualifyType()
    {
        if ($this->option('admin')) {
            $type = 'admin';
        }

        if ($this->option('api')) {
            $type = 'api';
        }

        if ($this->option('web')) {
            $type = 'web';
        }

        if (! isset($type) && (! $this->option('type') || ! in_array($this->option('type'), $this->types))) {
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
    protected function createRouteDirectoryifNotExists()
    {
        if (! is_dir($directory = $this->getPath('routes', $withExtension = false))) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function generateRouteFile()
    {
        $type = $this->option('type');

        if (file_exists($path = $this->getPath('routes/'.$type)) && ! $this->option('force')) {
            if (! $this->confirm("The [{$type}] route already exists. Do you want to replace it?")) {
                return;
            }
        }

        $replace = $this->buildReplacements();

        if (! file_exists(stubs_path('routes/'.$type.'.stub'))) {
            $type = 'route.empty';
        }

        $stub = str_replace(
            array_keys($replace),
            array_values($replace),
            $this->files->get(stubs_path('routes/'.$type.'.stub'))
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
        $name = strtolower(basename($this->module['path']));

        return [
            'dummy-singular-name' => str_singular($name),
            'dummy-plural-name' => str_plural($name),
            'DummyText' => ucwords($name),
        ];
    }
}
