<?php

namespace Core\Application;

use Core\Manifests\ModuleManifest;
use Core\Manifests\SidebarManifest;
use Core\Manifests\ThemeManifest;
use Core\Manifests\WidgetManifest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application as FoundationApplication;
use RuntimeException;

class Application extends FoundationApplication
{
    /**
     * The Application version.
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * Register the basic bindings into the container.
     *
     * @return void
     */
    protected function registerBaseBindings()
    {
        parent::registerBaseBindings();

        $this->instance(ModuleManifest::class, new ModuleManifest(
            new Filesystem, $this->modulesPath(), $this->getCachedModulesPath()
        ));

        $this->instance(ThemeManifest::class, new ThemeManifest(
            new Filesystem, $this->themesPath(), $this->getCachedThemesPath()
        ));

        $this->instance(SidebarManifest::class, new SidebarManifest(
            new Filesystem, $this->make(ModuleManifest::class), $this->getCachedSidebarPath()
        ));

        $this->instance(WidgetManifest::class, new WidgetManifest(
            new Filesystem, $this->make(ModuleManifest::class), $this->getCachedWidgetsPath()
        ));
    }

    /**
     * Bind all of the application paths in the container.
     *
     * @return void
     */
    protected function bindPathsInContainer()
    {
        parent::bindPathsInContainer();

        $this->instance('path.core', $this->corePath());

        $this->instance('path.modules', $this->modulesPath());

        $this->instance('path.theme', $this->themePath());

        $this->instance('path.themes', $this->themesPath());
    }

    /**
     * Get the path to the application "core" directory.
     *
     * @param  string $path Optionally, a path to append to the app path.
     * @return string
     */
    public function path($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'core'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the core files.
     *
     * @param  string $path Optionally, a path to append to the app path.
     * @return string
     */
    public function corePath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'core'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the modules files.
     *
     * @param  string $path Optionally, a path to append to the app path.
     * @return string
     */
    public function modulesPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'modules'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the core theme files.
     *
     * @param  string $path Optionally, a path to append to the app path.
     * @return string
     */
    public function themePath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'resources'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the themes files.
     *
     * @param  string $path Optionally, a path to append to the app path.
     * @return string
     */
    public function themesPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'themes'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path to the cached modules.php file.
     *
     * @return string
     */
    public function getCachedModulesPath()
    {
        return $this->bootstrapPath().'/cache/modules.php';
    }

    /**
     * Get the path to the cached sidebar.php file.
     *
     * @return string
     */
    public function getCachedSidebarPath()
    {
        return $this->bootstrapPath().'/cache/sidebar.php';
    }

    /**
     * Get the path to the cached themes.php file.
     *
     * @return string
     */
    public function getCachedThemesPath()
    {
        return $this->bootstrapPath().'/cache/themes.php';
    }

    /**
     * Get the path to the cached widgets.php file.
     *
     * @return string
     */
    public function getCachedWidgetsPath()
    {
        return $this->bootstrapPath().'/cache/widgets.php';
    }

    /**
     * Get the application namespace.
     *
     * @return string
     * @throws RuntimeException Runtime exception.
     */
    public function getNamespace()
    {
        if (! is_null($this->namespace)) {
            return $this->namespace;
        }

        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        foreach ((array) data_get($composer, 'autoload.psr-4') as $namespace => $path) {
            foreach ((array) $path as $pathChoice) {
                if (realpath(core_path()) == realpath(base_path().'/'.$pathChoice)) {
                    return $this->namespace = $namespace;
                }
            }
        }

        throw new RuntimeException('Unable to detect application namespace.');
    }
}
