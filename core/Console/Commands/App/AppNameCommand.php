<?php

namespace Core\Console\Commands\App;

use Illuminate\Foundation\Console\AppNameCommand as Command;

class AppNameCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'app:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the application namespace';

    /**
     * Set the bootstrap namespaces.
     *
     * @return void
     */
    protected function setBootstrapNamespaces()
    {
        $search = [
            $this->currentRoot.'\\Http',
            $this->currentRoot.'\\Console',
            $this->currentRoot.'\\Exceptions',
            $this->currentRoot.'\\Application',
        ];

        $replace = [
            $this->argument('name').'\\Http',
            $this->argument('name').'\\Console',
            $this->argument('name').'\\Exceptions',
            $this->argument('name').'\\Application',
        ];

        $this->replaceIn($this->getBootstrapPath(), $search, $replace);
    }
}
