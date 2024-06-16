<?php

namespace Announcement\Providers;

use Announcement\Jobs\DeleteExpiredAnnouncements;
use Announcement\Services\AnnouncementService;
use Announcement\Services\AnnouncementServiceInterface;
use Core\Providers\BaseServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class AnnouncementServiceProvider extends BaseServiceProvider
{
    /**
     * Array of providers to register.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Register any service class with its interface.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AnnouncementServiceInterface::class, AnnouncementService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Retrieve the array of factories.
     *
     * @return array
     */
    public function factories()
    {
        return [
            $this->directory('database/factories'),
        ];
    }

    /**
     * Get the base directory.
     *
     * @param  string $path
     * @return string
     */
    protected function directory(string $path = '')
    {
        return dirname(__DIR__).DIRECTORY_SEPARATOR.$path;
    }

    /**
     * Register the scheduled jobs.
     *
     * @return void
     */
    public function bootScheduledJobs()
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->job(new DeleteExpiredAnnouncements)->everyMinute();
        });
    }
}
