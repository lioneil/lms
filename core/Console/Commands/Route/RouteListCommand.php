<?php

namespace Core\Console\Commands\Route;

use Core\Manifests\ModuleManifest;
use Illuminate\Foundation\Console\RouteListCommand as Command;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class RouteListCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'route:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered routes';

    /**
     * The router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * An array of all the registered routes.
     *
     * @var \Illuminate\Routing\RouteCollection
     */
    protected $routes;

    /**
     * The ModuleManifest instance.
     *
     * @var \Core\Manifests\ModuleManifest
     */
    protected $module;

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = ['Module', 'Domain', 'Method', 'URI', 'Name', 'Action', 'Middleware'];

    /**
     * The columns to display when using the "compact" flag.
     *
     * @var array
     */
    protected $compactColumns = ['module', 'method', 'uri', 'action'];

    /**
     * Create a new route command instance.
     *
     * @param  \Illuminate\Routing\Router     $router
     * @param  \Core\Manifests\ModuleManifest $module
     * @return void
     */
    public function __construct(Router $router, ModuleManifest $module)
    {
        parent::__construct($router);

        $this->router = $router;
        $this->routes = $router->getRoutes();
        $this->module = $module;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        $this->info(" - {$this->getRoutesCount()} registered routes.");
    }

    /**
     * Count the number of entry in the routes.
     *
     * @return mixed
     */
    protected function getRoutesCount()
    {
        return count($this->getRoutes());
    }

    /**
     * Get the route information for a given route.
     *
     * @param  \Illuminate\Routing\Route $route
     * @return array
     */
    protected function getRouteInformation(Route $route)
    {
        return $this->filterRoute([
            'module' => $this->guessModuleFromActionName($route->getActionName()),
            'host'   => $route->domain(),
            'method' => implode('|', $route->methods()),
            'uri'    => $route->uri(),
            'name'   => $route->getName(),
            'action' => ltrim($route->getActionName(), '\\'),
            'middleware' => $this->getMiddleware($route),
        ]);
    }

    /**
     * Qualify and retrieve the module from a given route name.
     *
     * @param  string $name
     * @return string
     */
    protected function guessModuleFromActionName($name)
    {
        $names = explode('\\', $name);
        $module = $this->module->find(Str::singular($names[0] ?? null));

        if (is_null($module)) {
            return null;
        }

        if (is_dir($module['path'])) {
            return $module['name'];
        }

        return null;
    }

    /**
     * Filter the route by URI and / or name.
     *
     * @param  array  $route
     * @return array|null
     */
    protected function filterRoute(array $route)
    {
        if (($this->option('name') && ! Str::contains($route['name'], $this->option('name'))) ||
             $this->option('path') && ! Str::contains($route['uri'], $this->option('path')) ||
             $this->option('module') && ! Str::contains($route['module'], ucfirst($this->option('module'))) ||
             $this->option('method') && ! Str::contains($route['method'], strtoupper($this->option('method')))) {
            return null;
        }

        return $route;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['module', null, InputOption::VALUE_OPTIONAL, 'Filter the routes by module'],
        ]);
    }
}
