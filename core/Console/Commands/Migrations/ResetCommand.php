<?php

namespace Core\Console\Commands\Migrations;

use Core\Support\Module\ModuleTrait;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Console\Migrations\ResetCommand as BaseCommand;

class ResetCommand extends BaseCommand
{
    use ConfirmableTrait, ModuleTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migrate:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset and re-run all migrations';
}
