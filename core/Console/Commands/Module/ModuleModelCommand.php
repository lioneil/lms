<?php

namespace Core\Console\Commands\Module;

use Core\Console\Commands\QualifyModule;
use Core\Support\Module\ModuleTrait;
use Illuminate\Console\Command;
use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class ModuleModelCommand extends ModelMakeCommand
{
    use ModuleTrait, QualifyModule;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'module:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent model class for a module';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->qualifyModule();

        parent::handle();
    }
}
