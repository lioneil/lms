<?php

namespace Core\Console\Commands\Migrations;

use Core\Console\Commands\QualifyModule;
use Core\Support\Module\ModuleTrait;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand as BaseCommand;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class MigrateMakeCommand extends BaseCommand
{
    use ModuleTrait, QualifyModule;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:migration {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--module= : The module the file belongs to}
        {--m|modules : Indicate the migration file should be created for a module}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration}
        {--force : Create the class even if the file already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file';

    /**
     * The migration creator instance.
     *
     * @var \Illuminate\Database\Migrations\MigrationCreator
     */
    protected $creator;

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->usingModule() && is_null($this->input->getOption('path'))) {
            $this->qualifyModule();
        }

        parent::handle();
    }

    /**
     * Determine if the command is using module option.
     *
     * @return boolean
     */
    public function usingModule()
    {
        return $this->input->getOption('modules');
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return ! $this->usingRealPath()
                            ? $this->laravel->basePath().'/'.$targetPath
                            : $targetPath;
        }

        if (! is_null($targetPath = $this->input->getOption('module'))) {
            $targetPath .= DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'migrations';
            return $targetPath;
        }

        return parent::getMigrationPath();
    }

    /**
     * Determine if the given path(s) are pre-resolved "real" paths.
     *
     * @return boolean
     */
    protected function usingRealPath()
    {
        return $this->input->hasOption('realpath') && $this->option('realpath');
    }
}
