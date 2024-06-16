<?php

namespace Core\Console\Commands\Migrations;

use Core\Support\Module\ModuleTrait;
use Illuminate\Database\Console\Migrations\StatusCommand as BaseStatusCommand;

class StatusCommand extends BaseStatusCommand
{
    use ModuleTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migrate:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the status of each migration';
}
