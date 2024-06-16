<?php

namespace Core\Application\Widget;

use Core\Application\Breadcrumbs\Breadcrumbs;
use Core\Application\Widget\AbstractWidget;
use Core\Manifests\ModuleManifest;
use Core\Manifests\WidgetManifest;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class WidgetServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->bindRouteParameters();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerWidgetViewDirectory();

        $this->registerWidgetBindings();

        $this->registerWidgetBladeDirective();
    }

    /**
     * Bind the alias parameter to the core.widget class.
     *
     * @return void
     */
    protected function bindRouteParameters()
    {
        $this->app['router']->bind('widget', function ($alias) {
            return $this->app['core.widget']->find($alias);
        });
    }

    /**
     * Register the widgets folder with a namespace.
     *
     * @return void
     */
    protected function registerWidgetViewDirectory()
    {
        try {
            if (file_exists(theme()->active('views/widgets'))) {
                $this->loadViewsFrom(theme()->active('views/widgets'), 'widgets');
            }
        } catch (\Exception $e) {
            unset($e);
        }

        $this->loadViewsFrom(resource_path('views/widgets'), 'widgets');
    }

    /**
     * Bind default widget classes to the container.
     *
     * @return void
     */
    protected function registerWidgetBindings()
    {
        $this->app->singleton(Factories\WidgetFactory::class, function ($app) {
            return new Factories\WidgetFactory($app, $app[WidgetManifest::class]);
        });

        $this->app->singleton('core.widget', function ($app) {
            return new Factories\WidgetFactory($app, $app[WidgetManifest::class]);
        });

        $this->app->bind('manifest:widget', WidgetManifest::class);
    }

    /**
     * Register Blade directive.
     *
     * @return void
     */
    protected function registerWidgetBladeDirective()
    {
        Blade::directive('widget', function ($expression) {
            return "<?php echo app('core.widget')->make($expression); ?>";
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['core.widget'];
    }
}
