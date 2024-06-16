<?php

namespace Core\Console\Commands\Module;

use Core\Console\Commands\QualifyModule;
use Core\Support\Module\ModuleTrait;
use Illuminate\Console\Command;
use Illuminate\Foundation\Console\RequestMakeCommand;

class ModuleRequestCommand extends RequestMakeCommand
{
    use ModuleTrait, QualifyModule;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new form request class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

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
}
