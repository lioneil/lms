<?php

namespace Core\Manifests;

use Core\Manifests\ModuleManifest;
use Illuminate\Filesystem\Filesystem;

class SidebarManifest
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    public $files;

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
     * The array of disabled sidebar
     *
     * @var array
     */
    protected $disabledSidebar = [];

    /**
     * Create a new sidebar manifest instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  \Core\Manifests\ModuleManifest  $modules
     * @param  string  $manifestPath
     * @return void
     */
    public function __construct(Filesystem $files, ModuleManifest $modules, $manifestPath)
    {
        $this->files = $files;
        $this->modules = $modules;
        $this->manifestPath = $manifestPath;
    }

    /**
     * Build the manifest and write it to disk.
     *
     * @return void
     */
    public function build()
    {
        $sidebar = $this->getMenus();

        $ignoreAll = in_array('*', $ignore = $this->sidebarToIgnore());

        $this->write([

            'enabled' => collect($sidebar)->mapWithKeys(function ($sidebar) {
                    return $sidebar;
                })->each(function ($configuration) use (&$ignore) {
                    $ignore = array_merge($ignore, $configuration['disabled'] ?? []);
                })->reject(function ($configuration, $sidebar) use ($ignore, $ignoreAll) {
                    return $ignoreAll || in_array($sidebar, $ignore);
                })->filter()->all(),

            'disabled' => $ignore,

        ]);
    }

    /**
     * Retrieve the collection of sidebar.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return collect($this->getManifest()['enabled']);
    }

    /**
     * Retrieve a sidebar from a given sidebar name.
     *
     * @param string $sidebar
     * @return mixed
     */
    public function find($sidebar)
    {
        return $this->sidebar = $this->all()->filter(function ($item) use ($sidebar) {
            return $this->format($item['name']) === $this->format($sidebar);
        })->first();
    }

    /**
     * Alias for the method find.
     *
     * @param string $sidebar
     * @return mixed
     */
    public function sidebar($sidebar)
    {
        return $this->find($sidebar);
    }

    /**
     * Add to ignored sidebar the specified sidebar.
     *
     * @param string $sidebar
     * @return void
     */
    public function remove($sidebar)
    {
        $this->disabledSidebar = array_merge(
            $this->disabledSidebar,
            $this->all()->filter(function ($item) use ($sidebar) {
                return $this->format($item['name']) === $this->format($sidebar);
            })->pluck('name')->toArray()
        );

        $this->build();
    }

    /**
     * Retrieve the collection of disabled sidebar.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function disabled()
    {
        return collect($this->getManifest()['disabled']);
    }

    /**
     * Delete the generated manifest file from disk.
     *
     * @return void
     */
    public function destroy()
    {
        $this->files->delete($this->manifestPath);
    }

    /**
     * Get the current sidebar manifest.
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
     * Retrieve the array of sidebar manifest file
     * from sidebar folders.
     *
     * @return array
     */
    protected function getMenus()
    {
        return $this->modules->enabled()->map(function ($module) {
                if ($this->files->exists($module['path'].'/config/sidebar.php')) {
                    return $this->files->getRequire($module['path'].'/config/sidebar.php');
                }
                return [];
            })->filter(function ($module) {
                return ! empty($module);
            });
    }

    /**
     * Retrieve the manifest files from given path.
     *
     * @param string $path
     * @return array
     */
    protected function getSidebarConfigFiles($path = '')
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $option = \RecursiveIteratorIterator::SELF_FIRST;
        $iterators = new \RecursiveIteratorIterator($directory, $option);
        $files = new \RegexIterator($iterators, '/config\/sidebar\.php$/');

        $manifests = [];
        foreach ($files as $manifestPath => $file) {
            $manifest = $this->files->getRequire($file->getPathname());
            $manifests = array_merge($manifests, $manifest);
        }

        return $manifests ?? [];
    }

    /**
     * Format the given sidebar name.
     *
     * @param  string  $sidebar
     * @return string
     */
    protected function format($sidebar)
    {
        return strtolower($sidebar);
    }

    /**
     * Get all of the sidebar names that should be ignored.
     *
     * @return array
     */
    protected function sidebarToIgnore()
    {
        return $this->disabledSidebar ?? [];
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
            $this->manifestPath, '<?php return '.var_export($manifest, true).';'
        );
    }
}
