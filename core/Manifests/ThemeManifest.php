<?php

namespace Core\Manifests;

use Illuminate\Filesystem\Filesystem;

class ThemeManifest
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    public $files;

    /**
     * The themes path.
     *
     * @var string
     */
    public $themesPath;

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
     * The array of disabled themes
     *
     * @var string|null
     */
    protected $activeTheme;

    /**
     * The array of disabled themes
     *
     * @var array
     */
    protected $disabledThemes = [];

    /**
     * Create a new theme manifest instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @param  string                            $themesPath
     * @param  string                            $manifestPath
     * @return void
     */
    public function __construct(Filesystem $files, $themesPath, $manifestPath)
    {
        $this->files = $files;
        $this->themesPath = $themesPath;
        $this->manifestPath = $manifestPath;
    }

    /**
     * Build the manifest and write it to disk.
     *
     * @return void
     */
    public function build()
    {
        $themes = $this->getThemesFromFiles();

        $ignoreAll = in_array('*', $ignore = $this->themesToIgnore());

        $this->write([
            'enabled' => collect($themes)->mapWithKeys(function ($theme) {
                return [$this->format($theme['name']) => collect($theme)->except(['file'])->toArray() ?? []];
            })->each(function ($configuration) use (&$ignore) {
                $ignore = array_merge($ignore, $configuration['disabled'] ?? []);
            })->reject(function ($configuration, $theme) use ($ignore, $ignoreAll) {
                return $ignoreAll || in_array($theme, $ignore);
            })->filter()->all(),
            'disabled' => $ignore,
            'active' => $this->getActiveTheme(),
        ]);
    }

    /**
     * Retrieve the collection of themes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function themes()
    {
        return collect($this->getManifest()['enabled']);
    }

    /**
     * Retrieve a theme from a given theme name.
     *
     * @param  string $theme
     * @return mixed
     */
    public function find($theme)
    {
        return $this->theme = $this->themes()->filter(function ($item) use ($theme) {
            return $this->format($item['name']) === $this->format($theme);
        })->first();
    }

    /**
     * Alias for the method find.
     *
     * @param  string $theme
     * @return mixed
     */
    public function theme($theme)
    {
        return $this->find($theme);
    }

    /**
     * Add to ignored themes the specified theme.
     *
     * @param  string $theme
     * @return void
     */
    public function remove($theme)
    {
        $this->disabledThemes = array_merge(
            $this->disabledThemes,
            $this->themes()->filter(function ($item) use ($theme) {
                return $this->format($item['name']) === $this->format($theme);
            })->pluck('name')->toArray()
        );

        $this->build();
    }

    /**
     * Retrieve the collection of disabled themes.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function disabled()
    {
        return collect($this->getManifest()['disabled']);
    }

    /**
     * Put a theme to the `active` key
     * of the theme manifest file.
     *
     * @param  string $theme
     * @return boolean
     */
    public function activate($theme = 'default')
    {
        $this->setActiveTheme($this->find($theme));
        $this->build();

        return true;
    }

    /**
     * Retrieve the active theme.
     *
     * @return mixed
     */
    public function active()
    {
        if (is_null($this->getManifest()['active'])) {
            $this->activate();
        }

        return collect($this->getManifest()['active']);
    }

    /**
     * Retrieve the path to the current theme.
     *
     * @return string
     */
    public function getActiveThemePath(): string
    {
        return $this->theme['path'] ?? resource_path();
    }

    /**
     * Set the active theme variable.
     *
     * @param string $theme
     */
    protected function setActiveTheme($theme)
    {
        $this->activeTheme = $theme;
    }

    /**
     * Retrieve the active theme variable.
     *
     * @return string
     */
    protected function getActiveTheme()
    {
        return $this->activeTheme;
    }

    /**
     * Get the current theme manifest.
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
     * Retrieve the array of themes manifest file
     * from themes folders.
     *
     * @return array
     */
    protected function getThemesFromFiles()
    {
        return array_merge(
            $this->getManifestFiles(theme_path()),
            $this->getManifestFiles(themes_path())
        );
    }

    /**
     * Retrieve the manifest files from given path.
     *
     * @param  string $path
     * @return array
     */
    protected function getManifestFiles($path = '')
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $iterators = new \RecursiveIteratorIterator($directory, \RecursiveIteratorIterator::SELF_FIRST);
        $files = new \RegexIterator($iterators, '/theme\.json$/');

        $manifests = [];
        foreach ($files as $manifestPath => $file) {
            $manifest = (array) json_decode(file_get_contents($file->getPathname()), true);
            $manifests[$manifest['name']] = array_merge(
                (array) $manifest, [
                'name' => $manifest['name'],
                'preview' => (array) $manifest['preview'] ?? [],
                'author' => (array) $manifest['author'] ?? [],
                'colors' => (array) $manifest['colors'] ?? [],
                'path' => $file->getPath(),
                'manifest' => $manifestPath,
                'realpath' => $file->getRealpath(),
                'file' => $file->getPath(),
            ]);
        }

        return $manifests ?? [];
    }

    /**
     * Format the given theme name.
     *
     * @param  string $theme
     * @return string
     */
    protected function format($theme)
    {
        return ucfirst($theme);
    }

    /**
     * Get all of the theme names that should be ignored.
     *
     * @return array
     */
    protected function themesToIgnore()
    {
        return array_merge(
            config('themes.disabled', []),
            $this->disabledThemes
        );
    }

    /**
     * Write the given manifest array to disk.
     *
     * @param  array $manifest
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
