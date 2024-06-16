<?php

namespace Assessment\Providers;

use Assessment\Events\SubmissionSaved;
use Assessment\Listeners\SaveSubmissionToStatisticsTable;
use Core\Providers\EventServiceProvider as BaseEventServiceProvider;

class EventServiceProvider extends BaseEventServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SubmissionSaved::class => [
            SaveSubmissionToStatisticsTable::class,
        ],
    ];
}
