<?php

namespace Core\Console\Commands\Widgets;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class WidgetsClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'widgets:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the widget manifest cache file';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new route clear command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->files->delete($this->laravel->getCachedWidgetsPath());

        $this->info('Widget manifest cache cleared!');
    }
}
