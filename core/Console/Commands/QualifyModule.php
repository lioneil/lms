<?php

namespace Core\Console\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

trait QualifyModule
{
    /**
     * Get the module the file belongs to.
     *
     * @return string
     */
    protected function qualifyModule()
    {
        $this->callSilent('module:clear');
        $this->callSilent('module:discover');

        $module = $this->module($this->input->getOption('module'));

        $force = $this->input->hasOption('force') ? $this->input->getOption('force') : false;

        if (! empty($this->input->getOption('module')) && is_null($module) && ! $force) {
            $this->error("Module {$this->option('module')} not found!");
        }

        if (! $force && (is_null($module) || ! $module)) {
            $module = $this->module(
                $this->choice(
                    'Pick the module the file will belong',
                    $this->modules()->pluck('name')->toArray()
                )
            );
        }

        $this->input->setOption('module', $module['path'] ?? $this->option('module'));

        return $this->input->getOption('module');
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     * @param  string $withExtension
     * @return string
     */
    protected function getPath($name, $withExtension = true)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        $this->module['path'] = $this->module['path'] ?? $this->input->getOption('module');

        return $this->module['path'].'/'.str_replace('\\', '/', $name).($withExtension ? '.php' : null);
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return basename($this->option('module')).'\\';
    }

    /**
     * Get the module name for the class.
     *
     * @return string
     */
    protected function getModuleName()
    {
        return basename(dirname($this->getPath('module', false)));
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string $stub
     * @param  string $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            ['DummyModuleName', 'DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel'],
            [$this->getModuleName(), $this->getNamespace($name), $this->rootNamespace(), $this->userProviderModel()],
            $stub
        );

        return $this;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge([
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the file already exists'],
            ['module', null, InputOption::VALUE_OPTIONAL, 'Specify the module the resource will belong to'],
        ], parent::getOptions());
    }
}
