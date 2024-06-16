<?php

namespace Core\Console\Commands\Migrations;

use Core\Support\Module\ModuleTrait;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Console\Migrations\RollbackCommand as BaseCommand;

class RollbackCommand extends BaseCommand
{
    use ConfirmableTrait, ModuleTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migrate:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last database migration';
}
