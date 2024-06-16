<?php

namespace Core\Console\Commands\Widgets;

use Core\Manifests\WidgetManifest;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class WidgetsListCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'widgets:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered widgets stored in database';

    /**
     * The WidgetManifest instance.
     *
     * @var \Core\Manifests\WidgetManifest
     */
    protected $manifest;

    /**
     * Collection of widgets.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $widgets;

    /**
     * The table headers for the command.
     *
     * @var array
     */
    protected $headers = ['File', 'Namespace', 'Name', 'Alias'];

    /**
     * The columns to display when using the "compact" flag.
     *
     * @var array
     */
    protected $compactColumns = ['name', 'alias'];

    /**
     * Create a new command instance.
     *
     * @param  \Core\Manifests\WidgetManifest $manifest
     * @return void
     */
    public function __construct(WidgetManifest $manifest)
    {
        parent::__construct();

        $this->manifest = $manifest;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->widgets = $this->manifest->all();
        } catch (\Exception $e) {
            return;
        }

        if (empty($this->widgets)) {
            return $this->error("Your application doesn't have any widgets.");
        }

        if (empty($widgets = $this->getWidgets())) {
            return $this->error("Your application doesn't have any widgets matching the given criteria.");
        }

        $this->displayWidgets($widgets);
    }

    /**
     * Compile the widgets into a displayable format.
     *
     * @return array
     */
    protected function getWidgets()
    {
        $widgets = collect($this->widgets)->map(function ($widget) {
            return $this->getWidgetInformation($widget);
        })->filter()->all();

        if ($sort = $this->option('sort')) {
            $widgets = $this->sortWidgets($sort, $widgets);
        }

        if ($this->option('reverse')) {
            $widgets = array_reverse($widgets);
        }

        return $this->pluckColumns($widgets);
    }

    /**
     * Get the model information for a given widget.
     *
     * @param  array $widget
     * @return array
     */
    protected function getWidgetInformation($widget)
    {
        return $this->filterWidget([
            'file' => $widget['file'],
            'namespace' => $widget['namespace'],
            'name' => $widget['fullname'],
            'alias' => $widget['alias'],
        ]);
    }

    /**
     * Sort the widgets by a given element.
     *
     * @param  string $sort
     * @param  array  $widgets
     * @return array
     */
    protected function sortWidgets($sort, array $widgets)
    {
        return Arr::sort($widgets, function ($widget) use ($sort) {
            return $widget[$sort];
        });
    }

    /**
     * Remove unnecessary columns from the widgets.
     *
     * @param  array $widgets
     * @return array
     */
    protected function pluckColumns(array $widgets)
    {
        return array_map(function ($widget) {
            return Arr::only($widget, $this->getColumns());
        }, $widgets);
    }

    /**
     * Filter the widget by name or code.
     *
     * @param  array $widget
     * @return array|null
     */
    protected function filterWidget(array $widget)
    {
        if (($this->option('name') && ! Str::contains($widget['name'], $this->option('name'))) ||
             $this->option('alias') && ! Str::contains($widget['alias'], $this->option('alias'))) {
            return null;
        }

        return $widget;
    }

    /**
     * Get the table headers for the visible columns.
     *
     * @return array
     */
    protected function getHeaders()
    {
        return Arr::only($this->headers, array_keys($this->getColumns()));
    }

    /**
     * Get the column names to show (lowercase table headers).
     *
     * @return array
     */
    protected function getColumns()
    {
        $availableColumns = array_map('strtolower', $this->headers);

        if ($this->option('compact')) {
            return array_intersect($availableColumns, $this->compactColumns);
        }

        if ($columns = $this->option('columns')) {
            return array_intersect($availableColumns, $columns);
        }

        return $availableColumns;
    }

    /**
     * Display the widget information on the console.
     *
     * @param  array $widgets
     * @return void
     */
    protected function displayWidgets(array $widgets)
    {
        $this->table($this->getHeaders(), $widgets);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['columns', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Columns to include in the widget table'],

            ['compact', 'c', InputOption::VALUE_NONE, 'Only show compact column'],

            ['name', null, InputOption::VALUE_OPTIONAL, 'Filter the widgets by name'],

            ['alias', null, InputOption::VALUE_OPTIONAL, 'Filter the widgets by alias'],

            ['reverse', 'r', InputOption::VALUE_NONE, 'Reverse the ordering of the widgets'],

            ['sort', null, InputOption::VALUE_OPTIONAL, 'The column (name, alias) to sort by', 'alias'],
        ];
    }
}
