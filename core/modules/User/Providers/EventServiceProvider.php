<?php

namespace User\Providers;

use Core\Providers\BaseServiceProvider;
use Core\Providers\EventServiceProvider as BaseEventServiceProvider;
use User\Events\RefreshedPermissions;
use User\Events\UserUpdating;
use User\Listeners\SaveUserAccountToDetailsTable;
use User\Listeners\UpdateSuperadminPermissions;

class EventServiceProvider extends BaseEventServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        RefreshedPermissions::class => [
            UpdateSuperadminPermissions::class,
        ],

        UserUpdating::class => [
            SaveUserAccountToDetailsTable::class,
        ]
    ];
}
