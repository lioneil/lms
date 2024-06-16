<?php

namespace Course\Listeners;

use Course\Services\ContentServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Response;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ExtractLessonContentToStorage implements ShouldQueue
{
    /**
     * The Service class instance.
     *
     * @var \Course\Services\ContentServiceInterface
     */
    protected $service;

    /**
     * Create the event listener.
     *
     * @param  \Course\Services\ContentServiceInterface $service
     * @return void
     */
    public function __construct(ContentServiceInterface $service)
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
        $lesson = $event->lesson;

        $this->service->extractContentOr($lesson, function ($e) {
            Log::error($e->getMessage());

            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    }
}
