<?php

namespace Core\Console\Commands\Make;

use Illuminate\Foundation\Console\TestMakeCommand as Command;

class TestMakeCommand extends Command
{
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
