<?php

namespace Core\Providers;

use Core\Application\Breadcrumbs\BreadcrumbsServiceProvider;
use Core\Application\Repository\RepositoryServiceProvider;
use Core\Application\Sidebar\SidebarServiceProvider;
use Core\Application\Widget\WidgetServiceProvider;
use Core\Exceptions\RestrictedResourceException;
use Core\Providers\BaseServiceProvider;
use Core\Providers\ConsoleServiceProvider;
use Core\Providers\ThemeServiceProvider;
use Core\Repositories\AssetRepository;
use Core\Repositories\Contracts\AssetRepositoryInterface;
use Core\Repositories\Contracts\StorageRepositoryInterface;
use Core\Repositories\Contracts\ThemeRepositoryInterface;
use Core\Repositories\StorageRepository;
use Core\Repositories\ThemeRepository;
use Core\Services\FileService;
use Core\Services\FileServiceInterface;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\Finder\Finder;

class AppServiceProvider extends BaseServiceProvider
{
    /**
     * Array of providers to register.
     *
     * @var array
     */
    protected $providers = [
        BreadcrumbsServiceProvider::class,
        ConsoleServiceProvider::class,
        FactoryServiceProvider::class,
        ThemeServiceProvider::class,
        ModuleServiceProvider::class,
        PermissionServiceProvider::class,
        RepositoryServiceProvider::class,
        SearchServiceProvider::class,
        SettingServiceProvider::class,
        SidebarServiceProvider::class,
        WidgetServiceProvider::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->aliasBladeComponents();

        $this->aliasBladeIncludes();

        $this->loadBladeDirectives();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->bind(AssetRepositoryInterface::class, AssetRepository::class);
        $this->app->bind(StorageRepositoryInterface::class, StorageRepository::class);
        $this->app->bind(ThemeRepositoryInterface::class, ThemeRepository::class);
        $this->app->bind(FileServiceInterface::class, function ($app) {
            return new FileService($app['request'], $app['request']->file('file') ?: null);
        });
    }

    /**
     * Bootstrap any application factory files.
     *
     * @return array
     */
    public function factories(): array
    {
        return array_merge(config('database.factories', []), [
            base_path('database'.DIRECTORY_SEPARATOR.'factories'),
        ]);
    }

    /**
     * Register all view components
     * from resources/views/components folder.
     *
     * @return void
     */
    public function aliasBladeComponents()
    {
        $files = Finder::create()
                       ->in($this->app->themePath('views/components'))
                       ->name('*.blade.php');

        foreach ($files as $file) {
            if (file_exists($file->getRealPath())) {
                $component = basename($file->getRealPath(), '.blade.php');
                Blade::component("components.{$component}", $component);
            }
        }
    }

    /**
     * Register all view components
     * from resources/views/components folder.
     *
     * @return void
     */
    public function aliasBladeIncludes()
    {
        $files = Finder::create()
                       ->in($this->app->themePath('views/includes'))
                       ->name('*.blade.php');

        foreach ($files as $file) {
            if (file_exists($file->getRealPath())) {
                $component = basename($file->getRealPath(), '.blade.php');
                Blade::include("includes.{$component}", $component);
            }
        }
    }

    /**
     * Register additional blade directives from
     * the resources/views/directives folder.
     *
     * @return void
     */
    public function loadBladeDirectives()
    {
        $files = Finder::create()
                       ->in($this->app->themePath('views/directives'))
                       ->name('*.blade.php');

        foreach ($files as $file) {
            $component = basename($file->getRealPath(), '.blade.php');
            Blade::directive($component, function ($expression) use ($component) {
                $attributes = explode(',', $expression, 2);
                $params = ! empty($attributes[0]) ? "['param' => $attributes[0]]" : '[]';

                if (isset($attributes[1])) {
                    return "<?php echo view('directives.{$component}', array_merge($attributes[1], $params))->render(); ?>";
                }

                return "<?php echo view('directives.{$component}', $params)->render(); ?>";
            });
        }
    }
}
