<?php

namespace Course\Providers;

use Core\Providers\EventServiceProvider as BaseEventServiceProvider;
use Course\Events\LessonDeleted;
use Course\Events\LessonSaved;
use Course\Listeners\CreateUserCourseProgression;
use Course\Listeners\DeleteUserCourseProgression;
use Course\Listeners\ExtractLessonContentToStorage;
use Course\Listeners\UnlockNextLessonForUser;
use Course\Listeners\UpdateOrCreateUserCourseProgression;
use Course\Listeners\DeleteLessonFromCourseProgression;
use Subscription\Events\UserProgressed;
use Subscription\Events\UserSubscribed;
use Subscription\Events\UserUnsubscribed;

class EventServiceProvider extends BaseEventServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        LessonSaved::class => [
            ExtractLessonContentToStorage::class,
            UpdateOrCreateUserCourseProgression::class,
        ],
        LessonDeleted::class => [
            DeleteLessonFromCourseProgression::class,
        ],
        UserSubscribed::class => [
            CreateUserCourseProgression::class,
        ],
        UserUnsubscribed::class => [
            DeleteUserCourseProgression::class,
        ],
        UserProgressed::class => [
            UnlockNextLessonForUser::class,
        ],
    ];
}
