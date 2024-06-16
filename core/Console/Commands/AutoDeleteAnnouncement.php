<?php

namespace Core\Console\Commands;

use Announcement\Services\AnnouncementService;
use Illuminate\Console\Command;

class AutoDeleteAnnouncement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autodelete:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto delete announcement';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @param  \Announcement\Services\AnnouncementService $service
     * @return mixed
     */
    public function handle(AnnouncementService $service)
    {
        $service->autoclean();
    }
}
