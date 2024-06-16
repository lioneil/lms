<?php

namespace User\Observers;

use Illuminate\Http\Request;
use User\Events\UserUpdating;
use User\Models\User;

class UserObserver
{
    /**
     * Handle the user "recorded" event.
     *
     * @param  \User\Models\User $user
     * @return void
     */
    public function recorded(User $user)
    {
        event(new UserUpdating($user, $user->getEventData('recorded')));
    }

    /**
     * Handle the user "creating" event.
     *
     * @param  \User\Models\User $user
     * @return void
     */
    public function creating(User $user)
    {
        //
    }

    /**
     * Handle the user "created" event.
     *
     * @param  \User\Models\User $user
     * @return void
     */
    public function created(User $user)
    {

    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \User\Models\User $user
     * @return void
     */
    public function updated(User $user)
    {

    }
}
