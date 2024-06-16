<?php

namespace Announcement\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteExpiredAnnouncements implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @param  \Announcement\Services\AnnouncementServiceInterface $service
     * @return void
     */
    public function handle(AnnouncementServiceInterface $service)
    {
        $service->autoclean();
    }
}
