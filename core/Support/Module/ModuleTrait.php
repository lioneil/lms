<?php

namespace Core\Support\Module;

use BadMethodCallException;
use Core\Manifests\ModuleManifest;

trait ModuleTrait
{
    /**
     * The currently set module.
     *
     * @var array
     */
    protected $module;

    /**
     * Retrieve the modules from the ModuleManifest.
     *
     * @return \Illuminate\Support\Collection
     */
    public function modules()
    {
        return $this->manifest()->modules();
    }

    /**
     * Retrieve the module from the ModuleManifest.
     *
     * @param  mixed $module
     * @return array
     */
    public function module($module)
    {
        return $this->module = $this->manifest()->find($module);
    }

    /**
     * The ModuleManifest instance.
     *
     * @return \Core\Manifests\ModuleManifest
     */
    protected function manifest()
    {
        return $this->laravel->make('manifest:module');
    }

    /**
     * Retrieve the app's modules folder path.
     *
     * @param  string $path
     * @return string
     */
    protected function modulesPath(string $path = '')
    {
        return $this->laravel->modulesPath($path);
    }

    /**
     * Get all of the migration paths.
     *
     * @return array
     */
    protected function getMigrationPaths()
    {
        return array_merge(
            parent::getMigrationPaths(),
            $this->modules()->pluck('path')->map(function ($path) {
                return $path.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations';
            })->filter(function ($path) {
                return file_exists($path) && is_dir($path);
            })->toArray()
        );
    }
}
