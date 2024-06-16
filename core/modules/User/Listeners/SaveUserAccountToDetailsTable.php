<?php

namespace User\Listeners;

use Core\Enumerations\DetailType;
use Core\Enumerations\NameStandard;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use User\Events\UserUpdating;
use User\Services\DetailServiceInterface;

class SaveUserAccountToDetailsTable
{
    /**
     * The DetailService instance to be used.
     *
     * @var \User\Services\DetailServiceInterface
     */
    protected $service;

    /**
     * Create the event listener.
     *
     * @param  \User\Services\DetailServiceInterface $service
     * @return void
     */
    public function __construct(DetailServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param  \User\Events\UserUpdating $event
     * @return void
     */
    public function handle(UserUpdating $event)
    {
        if ($event->user->isNotSuperAdmin()) {
            $this->service->record($event->user, $event->attributes);
        }
    }
}
