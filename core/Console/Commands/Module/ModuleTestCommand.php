<?php

namespace Core\Console\Commands\Module;

use Core\Console\Commands\QualifyModule;
use Core\Support\Module\ModuleTrait;
use Illuminate\Foundation\Console\TestMakeCommand as Command;
use Illuminate\Support\Str;

class ModuleTestCommand extends Command
{
    use ModuleTrait, QualifyModule;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'module:test
                           {name : The name of the class}
                           {--m|module : Generate the test for the given module.}
                           {--unit : Create a unit test}
                           ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new test class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Test';

    /**
     * Execute the console command.
     *
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->qualifyModule();

        parent::handle();
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->module['path'].
            DIRECTORY_SEPARATOR.'tests'.
            DIRECTORY_SEPARATOR.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->option('unit')) {
            return stubs_path('unit-test.stub');
        }

        return stubs_path('test.stub');
    }
}
