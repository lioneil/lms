<?php

namespace Course\Jobs;

use Course\Services\CourseServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteExpiredCourses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @param  \Course\Services\CourseServiceInterface $service
     * @return void
     */
    public function handle(CourseServiceInterface $service)
    {
        $service->autoclean();
    }
}
