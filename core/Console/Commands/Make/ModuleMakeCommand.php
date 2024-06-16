<?php

namespace Core\Console\Commands\Make;

use Core\Console\Commands\QualifyModule;
use Core\Support\Module\ModuleTrait;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Input\InputOption;

class ModuleMakeCommand extends Command
{
    use ModuleTrait, QualifyModule;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module
                           {name : The name of the module}
                           {--extends= : Specify the module this module\'s controller, models, and such will extend to}
                           ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new module';

    /**
     * The module details to be
     * written to the module's manifest file.
     *
     * @var array
     */
    protected $details = [];

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The Composer class instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Manifest file name.
     *
     * @var string
     */
    protected $manifestFileName = 'manifest.json';

    /**
     * Create a new command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @param  \Illuminate\Support\Composer      $composer
     * @return void
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->qualifyModuleDetails();

        $this->generateFolders();

        $this->generateManifestFile();

        $this->generateConfigFiles();

        $this->generateModuleFiles();

        $this->composer->dumpAutoloads();
    }

    /**
     * Ask user for module details.
     *
     * @return void
     */
    protected function qualifyModuleDetails(): void
    {
        $this->details = [
            'description' => $this->ask(trans('console.description', ['name' => $this->argument('name')])),
            'author' => $this->ask(trans('console.author'), config('app.name')),
            'version' => $this->ask(trans('console.version'), '1.0.0'),
        ];
    }

    /**
     * Create module folder.
     *
     * @return void
     */
    protected function generateFolders()
    {
        $module = str_replace(' ', '', ucwords($this->argument('name')));

        $directories = [
            "$module/config",
            "$module/Http/Controllers",
            "$module/Http/Requests",
            "$module/database/factories",
            "$module/database/migrations",
            "$module/database/seeds",
            "$module/Models",
            "$module/Observers",
            "$module/Providers",
            "$module/Services",
            "$module/routes",
            "$module/views/admin",
        ];

        foreach ($directories as $directory) {
            $this->files->makeDirectory($this->modulesPath($directory), 0755, true, true);
            $this->line(' - Directory created at <fg=green>'.$directory.'</>');
        }
    }

    /**
     * Generate manifest file.
     *
     * @return void
     */
    protected function generateManifestFile()
    {
        $this->files->put($this->getModuleDirectory(), $this->getManifestData());

        $this->call('module:clear');
        $this->call('module:discover');

        $this->composer->dumpAutoloads();
    }

    /**
     * Generate config files:
     * - sidebar
     * - permissions
     *
     * @return void
     */
    protected function generateConfigFiles()
    {
        $modulePath = modules_path($this->argument('name'));

        $this->call('make:config', [
            '--type' => 'sidebar',
            '--module' => $modulePath,
            '--force' => true,
        ]);

        $this->call('make:config', [
            '--type' => 'permissions',
            '--module' => $modulePath,
            '--force' => true,
        ]);
    }

    /**
     * Generate controller file.
     *
     * @return void
     */
    protected function generateModuleFiles()
    {
        $modulePath = modules_path($this->argument('name'));

        $this->call('make:service', [
            'name' => $this->argument('name').'Service',
            '--module' => $modulePath,
            '--force' => true,
            '-n' => true,
        ]);

        $this->composer->dumpAutoloads();

        $this->call('module:controller', [
            'name' => $this->argument('name').'Controller',
            '--module' => $modulePath,
            '--admin' => true,
            '--force' => true,
            '-n' => true,
        ]);

        $this->call('module:model', [
            'name' => 'Models/'.$this->argument('name'),
            '--module' => $modulePath,
            '--force' => true,
            '-n' => true,
        ]);

        $this->call('module:views', [
            '--module' => $modulePath,
            '--force' => true,
            '-n' => true,
        ]);

        $this->composer->dumpAutoloads();

        $this->call('make:migration', [
            'name' => 'create_'.snake_case(str_plural($this->argument('name'))).'_table',
            '--module' => $modulePath,
            '--force' => true,
            '-n' => true,
        ]);

        $this->call('module:observer', [
            'name' => $this->argument('name').'Observer',
            '--module' => $modulePath,
            '--force' => true,
            '-n' => true,
        ]);

        $this->call('module:provider', [
            'name' => $this->argument('name').'ServiceProvider',
            '--module' => $modulePath,
            '--force' => true,
            '-n' => true,
        ]);

        $this->call('module:request', [
            'name' => $this->argument('name').'Request',
            '--module' => $modulePath,
            '--force' => true,
            '-n' => true,
        ]);

        $this->call('make:route', [
            '--admin' => true,
            '--module' => $modulePath,
            '--force' => true,
            '-n' => true,
        ]);
    }

    /**
     * Retrieve the manifest data.
     *
     * @return string
     */
    protected function getManifestData()
    {
        $data = array_merge(['name' => $this->argument('name')], $this->details);

        return preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Retrieve the module path.
     *
     * @return string
     */
    protected function getModuleDirectory()
    {
        return $this->modulesPath($this->argument('name').DIRECTORY_SEPARATOR.$this->manifestFileName);
    }
}
