<?php

namespace Core\Console\Commands\Optimize;

use Illuminate\Foundation\Console\OptimizeClearCommand as Command;

class OptimizeClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'optimize:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the cached bootstrap files';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('module:clear');
        $this->call('sidebar:clear');
        $this->call('widgets:clear');

        parent::handle();
    }
}
