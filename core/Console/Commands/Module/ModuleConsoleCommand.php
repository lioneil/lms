<?php

namespace Core\Console\Commands\Module;

use Core\Console\Commands\QualifyModule;
use Core\Support\Module\ModuleTrait;
use Illuminate\Console\Command;
use Illuminate\Foundation\Console\ConsoleMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class ModuleConsoleCommand extends ConsoleMakeCommand
{
    use ModuleTrait, QualifyModule;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Module command';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Console command';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException Throws an exception.
     */
    public function handle()
    {
        $this->qualifyModule();

        parent::handle();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['module', null, InputOption::VALUE_OPTIONAL, 'Generate a job for the given module.'],
        ]);
    }
}
