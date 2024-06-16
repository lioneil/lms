<?php

namespace Core\Console\Commands\Migrations;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:truncate {name : The table to be truncated (separated by comma)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Empty the specified tables';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tables = $this->argument('name') == '*' ? $this->tables() : explode(',', $this->argument('name'));
        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $this->info(" -- table '$table' has been truncated.");
        }
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Retrieve all tables.
     *
     * @return array
     */
    protected function tables()
    {
        return DB::connection()->getDoctrineSchemaManager()->listTableNames();
    }
}
