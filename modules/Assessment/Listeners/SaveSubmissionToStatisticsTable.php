<?php

namespace Assessment\Listeners;

use Assessment\Services\StatisticServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveSubmissionToStatisticsTable
{
    /**
     * The Service class instance.
     *
     * @var \Assessment\Services\StatisticServiceInterface
     */
    protected $service;

    /**
     * Create the event listener.
     *
     * @param  \Assessment\Services\StatisticServiceInterface $service
     * @return void
     */
    public function __construct(StatisticServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param  object $event
     * @return void
     */
    public function handle($event)
    {
        $submission = $event->submission;

        $attributes = [
            "key" => $submission->user_id,
            "value" => $submission->results,
            "metadata" => $submission->metadata,
            "statisticable_id" => $submission->submissible->getKey(),
            "statisticable_type" => get_class($submission->submissible),
        ];

        $this->service->store($attributes);
    }
}
