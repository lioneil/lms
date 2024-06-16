<?php

namespace Core\Manifests;

use Core\Manifests\ModuleManifest;
use Illuminate\Filesystem\Filesystem;

class WidgetManifest extends AbstractManifest
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    public $files;

    /**
     * The widgets path.
     *
     * @var string
     */
    public $widgetsPath;

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
     * The loaded module manifest.
     *
     * @var array
     */
    public $module;

    /**
     * The array of disabled widgets
     *
     * @var array
     */
    protected $disabledWidgets = [];

    /**
     * The default property key
     * for every widgets.
     *
     * @var string
     */
    protected $aliasProperty = 'alias';

    /**
     * Create a new widget manifest instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @param  \Core\Manifests\ModuleManifest    $module
     * @param  string                            $manifestPath
     * @return void
     */
    public function __construct(Filesystem $files, ModuleManifest $module, $manifestPath)
    {
        $this->files = $files;
        $this->module = $module;
        $this->manifestPath = $manifestPath;
    }

    /**
     * Build the manifest and write it to disk.
     *
     * @return void
     */
    public function build()
    {
        $widgets = $this->getWidgetsFromFiles();

        $ignoreAll = in_array('*', $ignore = $this->widgetsToIgnore());

        $this->write([
            'enabled' => $widgets,
            'disabled' => $ignore,
        ]);
    }

    /**
     * Retrieve the collection of widgets.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function widgets()
    {
        return collect($this->getManifest()['enabled']);
    }

    /**
     * Alias for widgets method.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function enabled()
    {
        return $this->widgets();
    }

    /**
     * Retrieve a widget from a given widget name.
     *
     * @param  string $widget
     * @return mixed
     */
    public function find($widget)
    {
        return $this->widget = $this->widgets()->filter(function ($item) use ($widget) {
            return $this->format($item['alias']) === $this->format($widget)
                || $this->format($item['fullname']) === $this->format($widget);
        })->first();
    }

    /**
     * Alias for the method find.
     *
     * @param string $widget
     * @return mixed
     */
    public function widget($widget)
    {
        return $this->find($widget);
    }

    /**
     * Add to ignored widgets the specified widget.
     *
     * @param  string $widget
     * @return void
     */
    public function remove($widget)
    {
        $this->disabledWidgets = array_merge(
            $this->disabledWidgets,
            $this->widgets()->filter(function ($item) use ($widget) {
                return $this->format($item['alias']) === $this->format($widget);
            })->pluck('alias')->toArray()
        );

        $this->build();
    }

    /**
     * Retrieve the collection of disabled widgets.
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
     * Retrieve the array of widgets manifest file
     * from widgets folders.
     *
     * @return array
     */
    protected function getWidgetsFromFiles()
    {
        return array_merge(
            $this->getManifestFiles($this->module->modules()->map(function ($module) {
                return $module['path'].DIRECTORY_SEPARATOR.'Widgets';
            })->flatten(1)->toArray()),
            $this->getManifestFiles(core_path('Widgets'))
        );
    }

    /**
     * Retrieve the manifest files from given path.
     *
     * @param  string $path
     * @return array
     */
    protected function getManifestFiles($path = [])
    {
        return collect($path)->filter(function ($module) {
            return file_exists($module);
        })->map(function ($module) {
            return $this->files->files($module);
        })->flatten(1)->map(function ($file) {
            return [
                'file' => $filename = $file->getRelativePathname(),
                'namespace' => $namespace = get_namespace($file->getPathname()),
                'fullname' => $class = $namespace.'\\'.basename($filename, '.php'),
                'name' => $alias = $this->getAliasFromClass($class),
                'alias' => $alias,
                'description' => $this->getDescriptionFromClass($class),
            ];
        })->toArray();
    }

    /**
     * Retrieve the property `alias` from
     * the given class string.
     *
     * @param  string $class
     * @return string
     */
    public function getAliasFromClass($class)
    {
        $class = new \ReflectionClass($class);

        return $class->getDefaultProperties()[$this->aliasProperty] ?? null;
    }

    /**
     * Retrieve the property `description` from
     * the given class string.
     *
     * @param  string $class
     * @return string
     */
    public function getDescriptionFromClass($class)
    {
        $class = new \ReflectionClass($class);

        return $class->getDefaultProperties()['description'] ?? null;
    }

    /**
     * Format the given widget name.
     *
     * @param  string $widget
     * @return string
     */
    protected function format($widget)
    {
        return ucfirst($widget);
    }

    /**
     * Get all of the widget names that should be ignored.
     *
     * @return array
     */
    protected function widgetsToIgnore()
    {
        return array_merge(
            config('widgets.disabled', []),
            $this->disabledWidgets
        );
    }

    /**
     * Write the given manifest array to disk.
     *
     * @param  array $manifest
     * @return void
     * @throws Exception  Directory must be writable.
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
