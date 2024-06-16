<?php

namespace Mail\Models;

use Core\Manifests\ModuleManifest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class Mail extends Collection
{
    /**
     * Create a new collection.
     *
     * @param  \Core\Manifests\ModuleManifest    $manifest
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @return void
     */
    public function __construct(ModuleManifest $manifest, Filesystem $files)
    {
        $this->items = $manifest->modules()->map(function ($module) {
            return $module['path'].DIRECTORY_SEPARATOR.'Mail';
        })->filter(function ($module) {
            return file_exists($module);
        })->mapWithKeys(function ($module, $key) use ($files) {
            return [$key => $files->files($module)];
        })->map(function ($files, $key) {
            $items = [];
            foreach ($files as $file) {
                $classFile = $key.'\Mail\\'.basename($file->getRelativePathName(), '.php');
                $items[] = new \ReflectionClass($classFile);
            }
            return $items;
        })->flatten(1);
    }
}
