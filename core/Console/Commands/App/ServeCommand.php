<?php

namespace Core\Console\Commands\App;

use Illuminate\Foundation\Console\ServeCommand as BaseCommand;
use Symfony\Component\Console\Input\InputOption;

class ServeCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Serve the application on the PHP development server';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve the application on', '0.0.0.0'],

            ['port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the application on', 8000],
        ];
    }
}
