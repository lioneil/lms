<?php

namespace Core\Manifests;

use Illuminate\Filesystem\Filesystem;

class ModuleManifest
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    public $files;

    /**
     * The modules path.
     *
     * @var string
     */
    public $modulesPath;

    /**
     * The manifest path.
     *
     * @var string|null
     */
    public $manifestPath;

    /**
     * The loaded manifest array.
     *
     * @var array
     */
    public $manifest;

    /**
     * The array of disabled modules
     *
     * @var array
     */
    protected $disabledModules = [];

    /**
     * Create a new module manifest instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $modulesPath
     * @param  string  $manifestPath
     * @return void
     */
    public function __construct(Filesystem $files, $modulesPath, $manifestPath)
    {
        $this->files = $files;
        $this->modulesPath = $modulesPath;
        $this->manifestPath = $manifestPath;
    }

    /**
     * Build the manifest and write it to disk.
     *
     * @return void
     */
    public function build()
    {
        $modules = $this->getModulesFromFiles();

        $ignoreAll = in_array('*', $ignore = $this->modulesToIgnore());

        $this->write([
            'enabled' => collect($modules)->mapWithKeys(function ($module) {
                return [$this->format($module['name']) => collect($module)->except(['file'])->toArray() ?? []];
            })->each(function ($configuration) use (&$ignore) {
                $ignore = array_merge($ignore, $configuration['disabled'] ?? []);
            })->reject(function ($configuration, $module) use ($ignore, $ignoreAll) {
                return $ignoreAll || in_array($module, $ignore);
            })->filter()->all(),
            'disabled' => $ignore,
        ]);
    }

    /**
     * Retrieve the collection of modules.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function modules()
    {
        return collect($this->getManifest()['enabled']);
    }

    /**
     * Alias for modules method.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function enabled()
    {
        return $this->modules();
    }

    /**
     * Retrieve a module from a given module name.
     *
     * @param string $module
     * @return mixed
     */
    public function find($module)
    {
        return $this->module = $this->modules()->filter(function ($item) use ($module) {
            return $this->format($item['name']) === $this->format($module);
        })->first();
    }

    /**
     * Alias for the method find.
     *
     * @param string $module
     * @return mixed
     */
    public function module($module)
    {
        return $this->find($module);
    }

    /**
     * Add to ignored modules the specified module.
     *
     * @param string $module
     * @return void
     */
    public function remove($module)
    {
        $this->disabledModules = array_merge(
            $this->disabledModules,
            $this->modules()->filter(function ($item) use ($module) {
                return $this->format($item['name']) === $this->format($module);
            })->pluck('name')->toArray()
        );

        $this->build();
    }

    /**
     * Retrieve the collection of disabled modules.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function disabled()
    {
        return collect($this->getManifest()['disabled']);
    }

    /**
     * Retrieve the contents of the given path.
     *
     * @param  string $file
     * @return mixed
     */
    public function getRequire(string $file)
    {
        return $this->files->getRequire($file);
    }

    /**
     * Get the current module manifest.
     *
     * @return array
     */
    protected function getManifest()
    {
        if (! is_null($this->manifest)) {
            return $this->manifest;
        }

        if (! file_exists($this->manifestPath)) {
            $this->build();
        }

        $this->files->get($this->manifestPath);

        return $this->manifest = file_exists($this->manifestPath) ?
            $this->files->getRequire($this->manifestPath) : [];
    }

    /**
     * Retrieve the array of modules manifest file
     * from modules folders.
     *
     * @return array
     */
    protected function getModulesFromFiles()
    {
        return array_merge(
            $this->getManifestFiles(core_path('modules')),
            $this->getManifestFiles(modules_path())
        );
    }

    /**
     * Retrieve the manifest files from given path.
     *
     * @param string $path
     * @return array
     */
    protected function getManifestFiles($path = '')
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $iterators = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST);
        $files = new \RegexIterator($iterators, '/manifest\.json$/');

        $manifests = [];
        foreach ($files as $manifestPath => $file) {
            $manifest = json_decode(file_get_contents($file->getPathname()));
            $manifests[$manifest->name] = array_merge(
                (array) $manifest,
                [
                    'namespace' => $manifest->name,
                    'path' => $file->getPath(),
                    'manifest' => $manifestPath,
                    'realpath' => $file->getRealpath(),
                    'code' => strtolower($manifest->name),
                    'file' => $file,
                ]
            );
        }

        return $manifests ?? [];
    }

    /**
     * Format the given module name.
     *
     * @param  string  $module
     * @return string
     */
    protected function format($module)
    {
        return ucfirst($module);
    }

    /**
     * Get all of the module names that should be ignored.
     *
     * @return array
     */
    protected function modulesToIgnore()
    {
        return array_merge(
            config('modules.disabled', []),
            $this->disabledModules
        );
    }

    /**
     * Write the given manifest array to disk.
     *
     * @param  array  $manifest
     * @return void
     *
     * @throws \Exception
     */
    protected function write(array $manifest)
    {
        if (! is_writable(dirname($this->manifestPath))) {
            throw new Exception('The '.dirname($this->manifestPath).' directory must be present and writable.');
        }

        $this->files->replace(
            $this->manifestPath,
            '<?php return '.var_export($manifest, true).';'
        );
    }
}
