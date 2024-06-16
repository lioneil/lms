<?php

namespace Core\Console\Commands\Widgets;

use Core\Console\Commands\QualifyModule;
use Core\Console\Commands\Widgets\WidgetMakeCommand;
use Core\Support\Module\ModuleTrait;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ModuleWidgetCommand extends WidgetMakeCommand
{
    use ModuleTrait, QualifyModule;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'module:widget';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new widget class for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Widget';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->qualifyModule();

        parent::handle();
    }

    /**
     * Get the destination class path.
     *
     * @param  string $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace(
            ['\\', '/'], '', $this->argument('name')
        );

        return $this->module['path']."/Widgets/{$name}.php";
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->module['namespace'].'\Widgets';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['module', 'm', InputOption::VALUE_OPTIONAL, 'Specify the module the resource will belong to.'],
        ]);
    }
}
