<?php

namespace Core\Console\Commands\Optimize;

use Illuminate\Foundation\Console\OptimizeCommand as Command;

class OptimizeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the framework bootstrap files';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('module:discover');
        $this->call('sidebar:cache');
        $this->call('theme:discover');
        $this->call('view:cache');

        parent::handle();
    }
}
